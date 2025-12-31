@php
    $isEditing = isset($existingCommissions) && $existingCommissions->isNotEmpty();
@endphp

@forelse($routes as $route)
    @php
        // Try to get existing commission data for this specific route ID
        $existing = $existingCommissions[$route->id] ?? [];

        // Use existing value or old input if available, otherwise default to 0
        $currentAcComm = $existing['ac_commission'] ?? 0;
        $currentNonAcComm = $existing['non_ac_commission'] ?? 0;

        // Determine if commission is currently set as a percentage (Logic placeholder)
        // Since your current DB only stores amounts, we assume a separate field/logic handles %
        $isPercentage = $route->pivot->is_percentage ?? false; 
    @endphp

    <tr>
        <td style="text-align: left;">
            {{ $route->name }}
            {{-- We don't need a hidden route input here, as the ID is in the input NAME --}}
        </td>

        {{-- 🚨 FIX 1: Use Route ID as the array key for commission_data --}}
        <td style="text-align: center;">
            <input type="text" class="form-control form-control-sm commission-input"
                name="commission_data[{{ $route->id }}][non_ac_commission]" placeholder="NonAC Comm. Amount"
                value="{{ old('commission_data.' . $route->id . '.non_ac_commission', $currentNonAcComm) }}">
        </td>

        {{-- 🚨 FIX 2: Use Route ID as the array key for AC commission --}}
        <td style="text-align: center;">
            <input type="text" class="form-control form-control-sm commission-input"
                name="commission_data[{{ $route->id }}][ac_commission]" placeholder="AC Comm. Amount"
                value="{{ old('commission_data.' . $route->id . '.ac_commission', $currentAcComm) }}">
        </td>

        {{-- Commission Type Checkbox (Optional logic) --}}
        <td style="text-align: center;">
            <label class="checkbox checkbox-outline checkbox-success">
                {{-- 🚨 FIX 3: Checkbox should also use the route ID as a key if status is stored per route --}}
                <input type="checkbox" class="form-control commission-checkbox"
                    name="commission_data[{{ $route->id }}][is_percentage]" value="1" {{ $isPercentage ? 'checked' : '' }}>
                <span></span> Percentage(%)
            </label>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center text-muted">No active routes found.</td>
    </tr>
@endforelse