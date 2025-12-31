{{-- This uses the structure shown in your image (4-column layout with center aisle) --}}

@if ($seatLayout)
    @php
        $totalRows = $seatLayout->total_rows ?? 9;
        $seatPattern = $seatLayout->seat_pattern ?? 'A,B,#,C,D'; // Example: A,B,#,C,D
        $seatPatternArray = explode(',', $seatPattern);
        $totalSeats = $seatLayout->total_seats ?? 'N/A';
        $busTypeClass = $busType ?? 'Economy Class';
        $alphabet = range('A', 'Z');
        $seatCount = 0;

        // Define simple column indices for seats based on the pattern (1, 2, 3, 4, etc.)
        $seatCols = 0;
        foreach ($seatPatternArray as $col) {
            if ($col !== '#') {
                $seatCols++;
            }
        }
    @endphp

    <style>
        .seat-btn {
            background-color: #f0f0f5;
            border: 1px solid #dcdcdc;
            border-radius: 5px;
            margin: 2px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 500;
            width: 45px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .seat-row-container {
            display: flex;
            justify-content: center;
            margin: 2px 0;
        }

        .seat-cell {
            padding: 2px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .seat-cell.aisle {
            width: 15px;
            flex-grow: 0;
        }

        .driver-seat {
            background-color: #dcdcdc;
            border-radius: 5px;
            font-size: 10px;
            padding: 5px;
            margin: 2px;
            width: 45px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .seat-grid-preview {
            /* Ensures the grid content is centralized and contained */
            max-width: 350px;
            margin: 0 auto;
        }
    </style>

    <div class="seat-map py-3 px-2">
        <div class="d-flex justify-content-center flex-wrap my-3">
            <span
                class="label label-inline label-light-danger font-weight-bold mx-1 my-1">{{ $seatLayout->deck_type ?? 'Single Deck' }}</span>
            <span class="label label-inline label-light-purple font-weight-bold mx-1 my-1">{{ $busTypeClass }}</span>
            <span class="label label-inline label-light-success font-weight-bold mx-1 my-1">Total Seats:
                {{ $totalSeats }}</span>
        </div>

        <div class="seat-grid-preview border border-dashed border-primary p-4">

            {{-- 1. DRIVER SEAT / FRONT ROW (Top Right) --}}
            <div class="seat-row-container justify-content-end mb-3">
                <div class="seat-cell aisle" style="flex-grow: 1;"></div>
                <div class="seat-cell">
                    <div class="driver-seat">#</div>
                </div>
            </div>

            {{-- 2. MAIN SEATING ROWS --}}
            @for ($r = 0; $r < $totalRows; $r++)
                @php $rowName = $alphabet[$r]; @endphp
                <div class="seat-row-container">
                    @php $currentCol = 1; @endphp
                    @foreach ($seatPatternArray as $columnDef)
                        @if ($columnDef == '#')
                            <div class="seat-cell aisle"></div>
                        @else
                            <div class="seat-cell">
                                @php $seatNumber = $rowName . $currentCol; @endphp
                                <button type="button" class="seat-btn bg-light-gray" data-seat="{{ $seatNumber }}">
                                    {{ $seatNumber }}
                                </button>
                            </div>
                            @php $currentCol++; @endphp
                        @endif
                    @endforeach
                </div>
            @endfor

            {{-- NOTE: For I2, I3, I4 seats that break the pattern,
            you would need special logic based on your SeatLayout model,
            or manually render them if they are static extras. --}}

        </div>

        <div class="text-right mt-3">
            <button type="button" class="btn btn-outline-success btn-sm">Edit Layout</button>
        </div>
    </div>
@else
    <div class="alert alert-danger text-center">
        Seat Layout configuration is missing or invalid. Please select Seat Plan and Bus Type again.
    </div>
@endif