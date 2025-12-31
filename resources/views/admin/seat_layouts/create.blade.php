@extends('layouts.master')

@section('title', 'Create New Seat Layout')

@section('content')
    <div class="container-fluid">
        <div class="card card-custom">
            <div class="card-header">
                <h3 class="card-title">Create New Seat Layout</h3>
            </div>
            
            {{-- Form: ACTION points to the store route --}}
            <form method="POST" action="{{ route('admin.seat_layouts.store') }}">
                @csrf
                
                <div class="card-body">
                    <div class="row">
                        
                        {{-- === LEFT COLUMN: Layout Details === --}}
                        <div class="col-md-6">
                            
                            {{-- Layout Name / Summary --}}
                            <div class="form-group">
                                <label for="name" class="required">Layout Name / Summary</label>
                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" 
                                       placeholder="e.g., 2 + 2 X 9 = 37" name="name" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Total Seats --}}
                            <div class="form-group">
                                <label for="total_seats" class="required">Total Seats</label>
                                <input type="number" id="total_seats"
                                       class="form-control @error('total_seats') is-invalid @enderror" placeholder="e.g., 37"
                                       name="total_seats" value="{{ old('total_seats') }}" required min="1">
                                @error('total_seats')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            {{-- Rows --}}
                            <div class="form-group">
                                <label for="rows" class="required">Total Rows (Per Deck)</label>
                                <input type="number" id="rows" class="form-control @error('rows') is-invalid @enderror"
                                       placeholder="e.g., 9" name="rows" value="{{ old('rows') }}" required min="1">
                                @error('rows')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Columns (Seats Per Row - e.g., 4 for 2+2) --}}
                            <div class="form-group">
                                <label for="columns" class="required">Seats Per Row (Columns)</label>
                                <input type="number" id="columns"
                                       class="form-control @error('columns') is-invalid @enderror" placeholder="e.g., 4"
                                       name="columns" value="{{ old('columns') }}" required min="1" max="5">
                                @error('columns')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- === RIGHT COLUMN: Deck Type, Class Type & Pattern Inputs (Matching Controller) === --}}
                        <div class="col-md-6">

                            {{-- Deck Type (ENUM) --}}
                            <div class="form-group">
                                <label for="deck_type" class="required">Deck Type</label>
                                <select id="deck_type" 
                                        class="form-control @error('deck_type') is-invalid @enderror"
                                        name="deck_type" 
                                        required
                                        onchange="toggleUpperDeck()">
                                    @foreach($availableDeckTypes as $type)
                                        <option value="{{ $type }}" {{ old('deck_type') == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('deck_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            {{-- Class Types (Multi-select) --}}
                            <div class="form-group">
                                <label for="class_types[]" class="required">Applicable Class Types</label>
                                <select id="class_types[]" 
                                        class="form-control  @error('class_types') is-invalid @enderror" 
                                        name="class_types[]" 
                                        multiple="multiple" 
                                        data-live-search="true"
                                        required>
                                    @foreach($availableClasses as $class)
                                        <option value="{{ $class }}" 
                                                {{ in_array($class, old('class_types', [])) ? 'selected' : '' }}>
                                            {{ $class }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_types')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- ⭐ Seat Pattern - LOWER DECK / SINGLE (Controller expects this name) ⭐ --}}
                            <div class="form-group">
                                <label for="seat_pattern_lower" class="required">Seat Pattern (Lower Deck / Single)</label>
                                <p class="text-muted small">কলাম লেবেল (A, B, #, C, D) কমা দিয়ে দিন।</p>
                                <input type="text" 
                                       id="seat_pattern_lower" 
                                       class="form-control @error('seat_pattern_lower') is-invalid @enderror"
                                       name="seat_pattern_lower" 
                                       placeholder="Example: A,B,#,C,D"
                                       value="{{ old('seat_pattern_lower') }}"
                                       required>
                                @error('seat_pattern_lower')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            
                            {{-- ⭐ CONDITIONAL INPUT: Upper Deck Pattern ⭐ --}}
                            <div id="upper_deck_container" 
                                 class="form-group" 
                                 style="display: {{ old('deck_type') == 'Double Deck' ? 'block' : 'none' }};">
                                 
                                <label for="seat_pattern_upper" class="required">Seat Pattern (Upper Deck)</label>
                                <p class="text-muted small">আপার ডেকের জন্য কলাম লেবেল দিন।</p>
                                <input type="text" 
                                       id="seat_pattern_upper" 
                                       class="form-control @error('seat_pattern_upper') is-invalid @enderror"
                                       name="seat_pattern_upper" 
                                       placeholder="Example: E,F,#,G,H"
                                       value="{{ old('seat_pattern_upper') }}">
                                @error('seat_pattern_upper')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mt-4">
                                <h4 class="mb-2">Seat Layout Preview:</h4>
                                <div id="seat_preview_area" class="border border-info bg-light p-3 text-muted text-center" style="min-height: 100px;">
                                    Preview is available in the list view after saving.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-pill btn-success">Save Layout</button>
                    <a class="btn btn-pill btn-secondary" href="{{ route('admin.seat_layouts.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Toggles visibility of the Upper Deck inputs based on Deck Type
    function toggleUpperDeck() {
        const deckTypeSelect = document.getElementById('deck_type');
        const upperDeckContainer = document.getElementById('upper_deck_container');
        const upperDeckInput = document.getElementById('seat_pattern_upper');
        
        // This check is now run only if the elements exist
        if (!deckTypeSelect || !upperDeckContainer || !upperDeckInput) return;

        const isDoubleDeck = deckTypeSelect.value === 'Double Deck';
        
        upperDeckContainer.style.display = isDoubleDeck ? 'block' : 'none';
        
        if (isDoubleDeck) {
            upperDeckInput.setAttribute('required', 'required');
        } else {
            upperDeckInput.removeAttribute('required');
            upperDeckInput.value = ''; // Clear value if hidden
        }
        
        // Re-initialize selectpicker if it's being used
        if (typeof $('.selectpicker').selectpicker === 'function') {
             $('.selectpicker').selectpicker('refresh');
        }
    }

    // Run on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initial run on page load to set correct visibility
        toggleUpperDeck(); 
    });
</script>
@endpush