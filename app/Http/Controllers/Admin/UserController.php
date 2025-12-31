<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
class UserController extends Controller
{
    /**
     * Display the form to create a new user.
     */
    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        // Assuming you have a Counter model/service to fetch counters
        // $counters = Counter::all(); 

        return view('admin.users.create', compact('roles', 'permissions' /*, 'counters' */));
    }

    public function forceLogout(User $user)
    {
        // The sessions table stores the user_id for authenticated sessions.
        // Deleting these records forces the user to log in again.

        // ⭐ Key Action: Delete all sessions associated with this user ID
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        // Redirect back to the user list with a confirmation message
        return redirect()->route('admin.users.index')->with(
            'success',
            'User ' . $user->username . ' has been successfully logged out across all devices.'
        );
    }

    public function index()
    {
        $users = User::with('roles')->get();
        return view('admin.users.index', compact('users'));
    }
    public function edit($id)
    {
        // 1. Fetch the user
        $user = User::findOrFail($id);

        // 2. Load dynamic options for the form
        $roles = Role::all();
        $permissions = Permission::all();
        $counters = Counter::all(); // ✅ Add this line

        // 3. Get user's current roles and permissions
        $userRoles = $user->getRoleNames()->toArray();
        $userPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

        // 4. Pass all data to the view
        return view('admin.users.edit', compact(
            'user',
            'roles',
            'permissions',
            'counters',        // ✅ Pass counters
            'userRoles',
            'userPermissions',
        ));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => [
                'nullable',
                'string',
                'max:15',
                Rule::unique('users', 'mobile_number')->ignore($user->id),
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'required|in:active,inactive,blocked',
            'counter_id' => 'nullable|exists:counters,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $data = $request->only(['name', 'mobile_number', 'username', 'status', 'counter_id']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data); // ✅ Counter will be saved here

        $user->syncRoles($request->roles);
        $user->syncPermissions($request->permissions ?: []);

        return redirect()->route('admin.users.index')->with('success', 'User details updated successfully!');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'nullable|string|max:15|unique:users,mobile_number',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'status' => 'required|in:active,inactive,blocked',
            'counter_id' => 'nullable|exists:counters,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'mobile_number' => $request->mobile_number,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'status' => $request->status,
            'counter_id' => $request->counter_id, // ✅ Save counter
        ]);

        $user->syncRoles($request->roles);

        if ($request->permissions) {
            $user->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully with assigned roles and permissions.');
    }

}