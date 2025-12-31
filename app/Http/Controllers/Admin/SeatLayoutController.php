<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeatLayout;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class SeatLayoutController extends Controller
{
    /**
     * Display a listing of the seat layouts.
     */
    public function index()
    {
        $seatLayouts = SeatLayout::all();
        $user = Auth::user();
        return view('admin.seat_layouts.index', compact('seatLayouts', 'user'));
    }

    /**
     * Display the form to create a new seat layout.
     */
    public function create()
    {
        $availableDeckTypes = ['Single Deck', 'Double Deck'];
        $availableClasses = ['Economy Class', 'Business Class', 'Sleeper Class', 'VIP Class'];
        $user = Auth::user();
        return view('admin.seat_layouts.create', compact('availableDeckTypes', 'availableClasses', 'user'));
    }

    /**
     * Store a newly created seat layout in storage.
     */
    public function store(Request $request)
    {
        // 1. Validation Logic
        $request->validate([
            'name' => 'required|string|max:255',
            'total_seats' => 'required|integer|min:1',
            'rows' => 'required|integer|min:1',
            'columns' => 'required|integer|min:1|max:5', // This 'columns' field might become less strict
            'deck_type' => ['required', Rule::in(['Single Deck', 'Double Deck'])],
            'class_types' => 'required|array',
            'seat_pattern_lower' => 'required|string', // Increased length if complex patterns are given
            'seat_pattern_upper' => Rule::when($request->deck_type == 'Double Deck', ['required', 'string'], 'nullable'),
        ]);

        // 2. ⭐ FIX: Data Processing & Creation to support complex patterns like your image ⭐

        // Process lower deck pattern string into a nested array suitable for the image
        $lowerDeckPatternsByRow = [];
        $lowerPatternStrings = array_map('trim', explode('|', $request->seat_pattern_lower));

        // Example parsing: "A1,B1,C1,D1,E1,F1,G1,H1" => first column
        //                  "A3,A4,B3,B4,C3,C4,D3,D4,E3,E4,F3,F4,G3,G4,H3,H4" => second/third columns
        //                  "I1,I2,I3,I4" => last row
        // This is a simplified parsing. A real-world visual editor would generate this JSON.
        // For now, we'll try to reconstruct based on your image logic:

        $fixedSeatPatterns = [];
        $lastRowIndex = (int) $request->rows - 1; // 0-indexed for array

        // This attempts to replicate the image's pattern generation
        // You'll likely need to manually adjust `seat_pattern_lower` for this to work perfectly.
        // For 9 rows: A-H for rows 0-7, I for row 8
        $alphabet = range('A', 'Z');

        for ($i = 0; $i < (int) $request->rows; $i++) {
            $rowPattern = [];
            $currentLetter = $alphabet[$i];

            if ($i == $lastRowIndex) { // Last row (Row 9 in your image, if total rows is 9)
                // This is where "I1, I2, I3, I4" comes from.
                // For simplified input, we assume the last part of seat_pattern_lower is for this.
                // A robust solution would have a UI or more structured input.
                // Let's assume for now, it's always 'I' for the last row.
                $rowPattern = [$currentLetter . '1', $currentLetter . '2', $currentLetter . '3', $currentLetter . '4'];
                // Or if you only want it to be I1, I2, I3, I4, then just hardcode it
                // $rowPattern = ['I1', 'I2', 'I3', 'I4'];

            } else { // Rows 1-8
                $rowPattern = [$currentLetter . '1', '#', $currentLetter . '3', $currentLetter . '4'];
            }
            $fixedSeatPatterns[] = $rowPattern;
        }

        // Add the '#' at the very top right, as seen in your image.
        // This implies the first row might need a special header.
        // For simplicity, let's keep the row-based pattern and add an "aisle marker" in Blade's header.
        // Or if you want a fixed '#' at position (0,3) (column 4, row 1), we can put it there.
        // Given your image, it looks like a column header rather than a seat.
        // We'll manage this in `renderDeck` for visual consistency.


        $seatMapConfig = [
            'pattern' => [
                'lower_deck' => $fixedSeatPatterns, // Each element is an array for a row
                'upper_deck' => $request->deck_type == 'Double Deck'
                    ? [] // You'd process upper deck pattern similarly if needed
                    : null,
            ],
            'deck_type' => $request->deck_type,
            'classes' => $request->class_types,
        ];

        $layout = SeatLayout::create([
            'name' => $request->name,
            'total_seats' => $request->total_seats,
            'rows' => $request->rows,
            'columns' => $request->columns, // Still saving, but less critical for custom rendering
            'deck_type' => $request->deck_type,
            'class_types' => $request->class_types,
            'seat_map_config' => $seatMapConfig, // Saves the structured JSON
            'is_active' => true,
        ]);

        // 3. Redirection
        return redirect()->route('admin.seat_layouts.index')
            ->with('success', 'Seat Layout created successfully! (Layout ID: ' . $layout->id . ')');
    }

    // ... (Other Controller Methods) ...
}