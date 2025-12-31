@extends('layouts.master')

@section('title', 'Seat Layout List')

@php
    function renderSeatDeck($seatMapConfig, $totalRows, $deckKey, $deckName)
    {
        $pattern = $seatMapConfig['pattern'][$deckKey] ?? [];

        if (!is_array($pattern) || empty($pattern) || $totalRows < 1) {
            return '<h6 class="text-danger text-center mt-2">No seats configured for ' . $deckName . '.</h6>';
        }

        $gridColumns = 'auto repeat(' . count($pattern) . ', 1fr)';

        $output = "<h4 class='text-center mt-3 mb-2'>{$deckName}</h4>";
        $output .= "<div class='seat-layout-grid' style='display: grid; grid-template-columns: {$gridColumns}; gap: 5px; margin: 10px auto; width: fit-content;'>";

        $rowLabels = range('A', chr(ord('A') + $totalRows - 1));

        for ($row = 0; $row < $totalRows; $row++) {
            $currentLabel = $rowLabels[$row];
            $output .= "<div class='row-label'>Row {$currentLabel}</div>";

            foreach ($pattern as $colLabel) {
                $trimmedLabel = trim($colLabel);

                if ($trimmedLabel === '#') {
                    $output .= "<div class='seat-cell seat-aisle'></div>";
                } else {
                    $seatName = $currentLabel . $trimmedLabel;
                    $output .= "<div class='seat-cell'>";
                    $output .= "<button type='button' class='seat-btn btn btn-sm btn-light p-1'>{$seatName}</button>";
                    $output .= "</div>";
                }
            }
        }

        $output .= '</div>';
        return $output;
    }
@endphp

@section('content')
    <div class="container-fluid">
        <div class="card card-custom">
            <div class="card-header">
                <h3 class="card-title">Seat Layout List</h3>
                <div class="card-toolbar">
                    <a class="btn btn-outline-primary" href="{{ route('admin.seat_layouts.create') }}">
                        <span class="fa fa-plus"></span> Create New Layout
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">

                    @forelse ($seatLayouts as $layout)
                                    <div class="col-md-4 my-2">

                                        <h5 class="text-center">
                                            {{ $layout->name ?? 'Layout #' . $layout->id }}
                                            (Seats: {{ $layout->total_seats ?? 'N/A' }})
                                        </h5>

                                        <div class="card border">
                                            <div class="card-body">

                                                {{-- Layout Info --}}
                                                <span class="badge badge-danger p-2 my-1">
                                                    {{ $layout->deck_type ?? 'N/A' }}
                                                </span>

                                                <span class="badge badge-info p-2 my-1">
                                                    @if (is_array($layout->class_types) && count($layout->class_types))
                                                        {{ implode(', ', $layout->class_types) }}
                                                    @else
                                                        No Classes
                                                    @endif
                                                </span>

                                                {{-- ⭐ Seat Map Preview ⭐ --}}
                                                <div class="seat-map-preview-area border border-info bg-light p-3 mt-3"
                                                    style="border-style: dashed !important;">

                                                    @php
                                                        $totalRows = (int) ($layout->rows ?? 0);
                                                        $seatConfig = is_array($layout->seat_map_config)
                                                            ? $layout->seat_map_config
                                                            : [];
                                                    @endphp

                                                    {{-- Lower Deck or Single Deck --}}
                                                    {!! renderDeck(
                            $seatConfig,
                            $totalRows,
                            'lower_deck',
                            $layout->deck_type == 'Double Deck' ? 'LOWER DECK' : 'SEAT MAP'
                        ) 
                                                                            !!}

                                                    {{-- Upper deck only if double deck --}}
                                                    @if ($layout->deck_type == 'Double Deck')
                                                        <hr class="my-3">
                                                        {!! renderDeck($seatConfig, $totalRows, 'upper_deck', 'UPPER DECK') !!}
                                                    @endif

                                                </div>

                                                {{-- Edit Button --}}
                                                <div class="mt-3 text-right">
                                                    <a href="{{ route('admin.seat_layouts.edit', $layout->id) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="far fa-edit"></i> Edit
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">No seat layouts found.</div>
                        </div>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .seat-layout-grid {
            display: grid;
            gap: 6px;
            margin: 10px auto;
            padding: 10px;
            background: #f7fcfe;
        }

        .row-label {
            font-size: 11px;
            font-weight: bold;
            text-align: right;
            padding-right: 5px;
            align-self: center;
            color: #444;
        }

        .seat-cell {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .seat-aisle {
            width: 35px;
            height: 35px;
            visibility: hidden;
        }

        .seat-btn {
            width: 35px;
            height: 35px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: bold;
            padding: 0;
            border: 1px solid #bbb;
            background: #e8e8e8;
            color: #222;
        }
    </style>
@endpush