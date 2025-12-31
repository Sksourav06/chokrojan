<div class="station-counter-wrapper">
    @foreach ($stations as $station)
        <div class="card mb-3 shadow-sm border rounded p-3">
            {{-- 🔹 Station Header --}}
            <div class="d-flex align-items-center mb-2">
                <div class="d-flex align-items-center flex-grow-1">
                    <div class="me-2">
                        @if ($loop->first)
                            <i class="fas fa-map-marker-alt text-success"></i>
                        @elseif ($loop->last)
                            <i class="fas fa-map-marker-alt text-danger"></i>
                        @else
                            <i class="fas fa-map-marker-alt text-primary"></i>
                        @endif
                    </div>
                    <div class="fw-bold me-3">{{ $station->required_time ?? '—' }}</div>
                    <div class="fw-semibold">{{ $station->name }}</div>
                </div>

                {{-- ➕ Add Counter Button --}}
                <button class="btn btn-sm btn-light-success add-counter-btn" data-station="{{ $station->id }}">
                    <i class="fas fa-plus"></i>
                </button>
            </div>

            {{-- 🔹 Counter Table --}}
            <div class="table-responsive">
                <table class="table table-sm align-middle table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 30%">Counter Name</th>
                            <th style="width: 20%">From Time</th>
                            <th style="width: 20%">To Time</th>
                            <th style="width: 10%" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="counter-list" data-station="{{ $station->id }}">
                        @forelse ($station->counters as $counter)
                            <tr>
                                <td>
                                    <input type="text" class="form-control form-control-sm counter-name"
                                        value="{{ $counter->name }}" readonly>
                                </td>
                                <td>
                                    <input type="time" class="form-control form-control-sm from-time"
                                        value="{{ $counter->from_time }}">
                                </td>
                                <td>
                                    <input type="time" class="form-control form-control-sm to-time"
                                        value="{{ $counter->to_time }}">
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary update-btn" data-id="{{ $counter->id }}">
                                        <i class="fas fa-save"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light-danger remove-counter-btn" data-id="{{ $counter->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-muted text-center">
                                    No counters found for this station.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    {{-- Footer buttons --}}
    <div class="d-flex justify-content-end mt-3">
        <button class="btn btn-success me-2 px-4">Submit</button>
        <button class="btn btn-warning me-2 px-4">Discard</button>
        <button class="btn btn-light px-4">Cancel</button>
    </div>
</div>

{{-- ✅ Styling --}}
<style>
    .station-counter-wrapper {
        background-color: #f9f9f9;
        border-radius: 1rem;
        padding: 1.5rem;
    }

    .card {
        border-radius: 0.75rem !important;
    }

    .form-select-sm,
    .form-control-sm {
        border-radius: 0.5rem;
    }

    .btn-light-success {
        background-color: #e8f9ee;
        color: #198754;
    }

    .btn-light-success:hover {
        background-color: #d4f5e0;
    }

    .btn-light-danger {
        background-color: #fce8e8;
        color: #dc3545;
    }
</style>