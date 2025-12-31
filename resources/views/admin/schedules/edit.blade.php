@extends('layouts.master')

@section('title', 'Schedule Update Manager')

@section('content')
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

            {{-- Subheader / Tabs Header / Tab 1-5 Contents are assumed correct --}}

            <div class="d-flex flex-column-fluid">
                <div class="container-fluid">
                    <div class="card card-custom" id=" content-card">

                        <div class="card-header card-header-tabs-line">
                            <h3 class="card-title">
                                {{ $schedule->name }} [{{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}]
                            </h3>
                            <div class="card-toolbar">
                                <ul class="nav nav-tabs nav-bold nav-tabs-line nav-success">
                                    <li class="nav-item"><a class="nav-link active py-6" data-toggle="tab"
                                            href="#kt_tab_pane_1"><i class="fas fa-atom me-1"></i> General Info</a></li>
                                    <li class="nav-item"><a class="nav-link py-6" data-toggle="tab" href="#kt_tab_pane_2"><i
                                                class="fas fa-store-alt me-1"></i> Station & Counters</a></li>
                                    <li class="nav-item"><a class="nav-link py-6" data-toggle="tab" href="#kt_tab_pane_3"><i
                                                class="fas fa-user-check me-1"></i> Counter Permissions</a></li>
                                    <li class="nav-item"><a class="nav-link py-6" data-toggle="tab" href="#kt_tab_pane_4"><i
                                                class="fas fa-globe me-1"></i> Online Permissions</a></li>
                                    <li class="nav-item"><a class="nav-link py-6" data-toggle="tab" href="#kt_tab_pane_5"><i
                                                class="fas fa-shipping-fast me-1"></i> Schedule On/Off</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                                @include('admin.schedules.partials.tab_general_info')
                            </div>

                            {{-- === TAB 2: Station & Counters (Data Target) === --}}
                           <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
        <form id="counter-update-form-tab2" method="POST" action="{{ route('admin.schedules.updateCounters', $schedule->id) }}">
            @csrf
            <div class="card-body">
                @isset($stations)
                    @foreach($stations as $index => $station)
                        @php
                            $stationId = data_get($station, 'id');
                            $stationName = data_get($station, 'name');
                            $assignedRecords = $scheduleCounterList->where('station_id', $stationId);
                        @endphp

                        <div class="d-flex align-items-center mb-3 mt-4">
                            <span class="badge badge-pill badge-primary px-4 py-2 mr-2" style="font-size: 14px;">
                                <i class="fas fa-map-marker-alt mr-1"></i> {{ $stationName }}
                            </span>
                            <button type="button" class="btn btn-sm btn-success rounded-circle add-new-row" data-station-id="{{ $stationId }}">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>

                        <div id="station_rows_{{ $stationId }}" class="station-rows-wrapper">
                            @if($assignedRecords->count() > 0)
                                @foreach($assignedRecords as $subIndex => $record)
                                    @include('admin.schedules.partials.counter_row_ui', [
                                        'station' => $station,
                                        'record' => $record,
                                        'rowIndex' => $stationId . '_' . $subIndex
                                    ])
                                @endforeach
                            @else
                                {{-- ডেটা না থাকলে অন্তত একটি খালি রো --}}
                                @include('admin.schedules.partials.counter_row_ui', [
                                    'station' => $station,
                                    'record' => null,
                                    'rowIndex' => $stationId . '_0'
                                ])
                            @endif
                        </div>
                    @endforeach
                @endisset
            </div>

            <div class="card-footer d-flex justify-content-start">
                <button type="submit" class="btn btn-success px-10 mr-2">Submit</button>
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary px-10">Cancel</a>
            </div>
        </form>
    </div>

                            {{-- === TAB 3, 4, 5 Contents (Assumed correct) === --}}
                            {{-- === TAB 3: Counter Permissions & Seat Blocking === --}}
                            <div class="tab-pane fade" id="kt_tab_pane_3" role="tabpanel">
    <div class="card">
        <div class="card-body">
            <div class="d-flex gap-2 flex-wrap mb-3">
                <button type="button" id="select_all_counters" class="btn btn-sm btn-warning">Select All Counters</button>
                <button type="button" id="deselect_all_counters" class="btn btn-sm btn-secondary">Deselect All Counters</button>
                <button type="button" id="save_counter_permissions_btn" class="btn btn-success btn-sm ms-auto">Save Permissions</button>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <h6 class="mb-3">Route Station Wise Counter List</h6>
                    <div id="route_station_permissions_container">
                        @foreach($stations as $station)
                            @php
                                $stationId = data_get($station, 'id');
                                $stationName = data_get($station, 'name');
                                $availableCounters = data_get($station, 'available_counters', []);
                            @endphp
                            <div class="card mb-2 border-light station-card">
                                <div class="card-header p-2 bg-light d-flex align-items-center">
                                    <input type="checkbox" class="station-check-all mr-2" data-id="{{ $stationId }}">
                                    <strong class="text-primary">{{ $stationName }}</strong>
                                </div>
                                <div class="list-group list-group-flush" id="perm_st_counters_{{ $stationId }}">
                                    @foreach($availableCounters as $c)
                                        <div class="list-group-item d-flex align-items-center py-2 counter-permission-row" data-counter-id="{{ $c->id }}">
                                            <input type="checkbox" class="counter-perm-cb mr-3" value="{{ $c->id }}">
                                            <div class="flex-grow-1">
                                                <div class="font-weight-bold">{{ $c->name }} <small class="text-muted">({{ $c->counter_type }})</small></div>
                                                <div class="d-flex gap-2 mt-1">
                                                    <input type="date" class="form-control form-control-sm perm-from" placeholder="From Date" style="width: 130px;">
                                                    <input type="date" class="form-control form-control-sm perm-to" placeholder="To Date" style="width: 130px;">
                                                    <button type="button" class="btn btn-xs btn-outline-info open-seat-modal-btn" 
                                                        data-id="{{ $c->id }}" data-name="{{ $c->name }}">
                                                        <i class="fas fa-th"></i> Seats
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-4">
                    <h6 class="mb-3 text-center">Counter Permitted Seats</h6>
                    <div id="counter_permitted_seats_container" class="border rounded p-3 bg-light" style="min-height:300px;">
                        <div id="seat_grid_display" class="seat-grid">
                            <p class="text-muted text-center pt-5">Select a counter to view or edit permitted seats</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                            {{-- === TAB 4 & 5: Placeholder Contents === --}}
                                  <div class="tab-pane fade" id="kt_tab_pane_4" role="tabpanel">
    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">Online Ticketing Platform Permissions</h5>

            <table class="table table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th>Platform</th>
                        <th class="text-center">Active</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Blocked Seats</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="platform_permission_table">
                    @php
                        // ধরুন $platforms collection এ platform objects আছে id সহ
                        // উদাহরণ: ['id'=>1,'name'=>'Website']
                        $platforms = [
                            ['id'=>1, 'name'=>'Website'],
                            ['id'=>2, 'name'=>'Shohoz'],
                            ['id'=>3, 'name'=>'Busbd'],
                            ['id'=>4, 'name'=>'Jatri'],
                            ['id'=>5, 'name'=>'Bdtickets'],
                        ];
                    @endphp

                    @foreach($platforms as $platform)
                        @php
                            $saved = $schedulePlatformList->where('platform_id', $platform['id'])->first();
                        @endphp
                       <tr class="platform-row" data-platform-id="{{ $platform['id'] }}">
    <td class="fw-bold">{{ $platform['name'] }}</td>
    <td class="text-center">
        <input type="checkbox" class="platform-active-cb" {{ $saved && $saved->status ? 'checked' : '' }}>
    </td>
    <td>
        <input type="date" class="form-control form-control-sm platform-from" value="{{ $saved->from_date ?? '' }}">
    </td>
    <td>
        <input type="date" class="form-control form-control-sm platform-to" value="{{ $saved->to_date ?? '' }}">
    </td>
    <td>
        <span class="badge badge-light-primary blocked-count">
            {{ $saved && $saved->blocked_seats ? count($saved->blocked_seats) : 0 }} Seats
        </span>
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-icon btn-outline-info open-platform-seats" 
                data-platform-id="{{ $platform['id'] }}">
            <i class="fas fa-th"></i>
        </button>
    </td>
</tr>

                    @endforeach
                </tbody>
            </table>

            <button id="save_platform_permissions" class="btn btn-success mt-3">
                <i class="fas fa-save mr-1"></i> Save Permissions
            </button>
        </div>
    </div>
</div>

                            <div class="tab-pane fade" id="kt_tab_pane_5" role="tabpanel">
                                <div class="card p-3">

                                    <h5 class="mb-4">Schedule On/Off Calendar</h5>

                                    <div class="row">

                                        {{-- Calendar --}}
                                        <div class="col-md-6">
                                            <div id="schedule_calendar" class="border rounded p-3" style="min-height:350px;">
                                                {{-- Calendar auto load by JS --}}
                                            </div>
                                        </div>

                                        {{-- On/Off List --}}
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5>Schedule On/Off List</h5>
                                                <button class="btn btn-sm btn-success" id="openOnDayModal">+</button>
                                            </div>

                                            <div id="onoff_list_container">
                                                {{-- Ajax list load here --}}
                                            </div>
                                        </div>

                                    </div>
                                </div>


                                {{-- Add New On Days Modal --}}
                                <div class="modal fade" id="onDayModal" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content" style="border-radius:12px;">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title">Add New On Days</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label>From Date</label>
                                                        <input type="date" id="from_date" class="form-control">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label>To Date</label>
                                                        <input type="date" id="to_date" class="form-control">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label>Start Time</label>
                                                        <input type="time" id="start_time" class="form-control">
                                                    </div>
                                                </div>

                                                <hr>

                                                <label class="mb-2">Week Days</label>
                                                <div class="d-flex flex-wrap gap-3">
                                                    @php 
                                                        $days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]; 
                                                    @endphp
                                                    @foreach($days as $day)
                                                        <label>
                                                            <input type="checkbox" class="weekday" value="{{ $day }}" checked> {{ $day }}
                                                        </label>
                                                    @endforeach
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button class="btn btn-success" id="saveOnDay">SAVE</button>
                                                <button class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('admin.schedules.partials.seat_blocking_modal')
        </div>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script>
document.addEventListener('DOMContentLoaded', function () {

    // === Add new counter row ===
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.add-new-row');
        if (!btn) return;

        e.preventDefault();

        const stationId = btn.getAttribute('data-station-id');
        const container = document.getElementById('station_rows_' + stationId);

        if (!container) {
            console.error('Container not found for station:', stationId);
            return;
        }

        const firstRow = container.querySelector('.counter-item-row');
        if (!firstRow) {
            console.error('Template row missing');
            return;
        }

        const clone = firstRow.cloneNode(true);
        const newIndex = stationId + '_' + Date.now();

        // Clear inputs
        clone.querySelectorAll('input').forEach(i => i.value = '');
        clone.querySelectorAll('select').forEach(s => s.value = '');

        // Update names
        clone.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/counter_data\[[^\]]+\]/, 'counter_data[' + newIndex + ']');
        });

        container.appendChild(clone);

        // Refresh selectpicker if exists
        if (window.jQuery && jQuery.fn.selectpicker) {
            jQuery(clone).find('.selectpicker').selectpicker('refresh');
        }
    });

    // === Remove counter row ===
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-row');
        if (!btn) return;

        const row = btn.closest('.counter-item-row');
        if (row) {
            const container = row.parentElement;
            if (container.querySelectorAll('.counter-item-row').length > 1) {
                row.remove();
            } else {
                // Reset last row
                row.querySelectorAll('input').forEach(i => i.value = '');
                row.querySelectorAll('select').forEach(s => s.value = '');
                if (window.jQuery && jQuery.fn.selectpicker) {
                    jQuery(row).find('.selectpicker').selectpicker('refresh');
                }
            }
        }
    });

    // === Initialize selectpicker on page load ===
    if (window.jQuery && jQuery.fn.selectpicker) {
        $('.selectpicker').selectpicker();
    }

    // === Counter form AJAX submit ===
    const form = document.getElementById('counter-update-form-tab2');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    Swal.fire('Success', data.message, 'success').then(() => {
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        }
                    });
                } else {
                    Swal.fire('Error', data.message || 'Unknown error', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Something went wrong', 'error');
            });
        });
    }

    // === Platform permissions AJAX ===
    $('#save_platform_permissions').on('click', function () {
    const btn = $(this);
    let platformData = [];

    $('.platform-row').each(function () {
        const row = $(this);
        platformData.push({
            platform_id: row.data('platform-id'), // numeric id, foreign key অনুযায়ী
            status: row.find('.platform-active-cb').is(':checked') ? 1 : 0,
            from_date: row.find('.platform-from').val(),
            to_date: row.find('.platform-to').val(),
            blocked_seats: [], // যদি blocked seats handle করো পরে
        });
    });

    $.ajax({
        url: "{{ route('admin.schedules.updatePlatformPermissions', $schedule->id) }}",
        method: 'POST',
        data: {
            _token: "{{ csrf_token() }}",
            platforms: platformData
        },
        beforeSend: function () {
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        },
        success: function (res) {
            btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Permissions');
            if (res.status == 'success') {
                Swal.fire('Success', 'Platform permissions updated!', 'success');
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        },
        error: function () {
            btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Permissions');
            Swal.fire('Error', 'Internal Server Error', 'error');
        }
    });
});



    // Optional: Platform seat button
    $(document).on('click', '.open-platform-seats', function() {
        const platform = $(this).data('platform');
        alert("Select permitted seats for: " + platform);
    });

});
</script>

@endsection

<!-- @section('scripts')
    {{-- 1. CRITICAL: Define ALL PHP variables and URLs --}}
    <script>
        window.routeStationList = @json($routeStationList ?? []);
        window.routeStationCounters = @json($routeStationCounters ?? []);
        window.scheduleCounterList = @json($scheduleCounterList ?? []);
        const scheduleCounterList = @json($scheduleCounterList ?? []);
        const seatLayoutData = @json($seatLayout ?? []);

        // Define AJAX URLs
        const getCounterPermissionsUrl = "{{ route('admin.schedules.getCounterPermissions', $schedule->id) }}";
        const updateCounterPermissionsUrl = "{{ route('admin.schedules.updateCounterPermissions', $schedule->id) }}";
        const getSeatLayoutUrl = "{{ route('admin.getSeatLayout', 'COUNTER_ID') }}";
        const saveBlockedSeatsUrl = "{{ route('admin.saveBlockedSeats') }}";

        // Global state variables (defined here so they are always available)
        let hasChanges = false;
        let currentCounterPermissionsData = [];
        let selectedCounterIdForBlocking = null;

        // Debugging to confirm data is received (check your console!)
        console.log('Tab 2 Data Received:', routeStationList);
        console.log('Tab 3 Data Received:', scheduleCounterList);

        (function ($) {
            // Configuration from Blade (make sure these variables are present in your blade)
            const scheduleId = @json($schedule->id ?? null);
            const getCounterPermissionsUrl = "{{ route('admin.schedules.getCounterPermissions', $schedule->id) }}";
            const updateCounterPermissionsUrl = "{{ route('admin.schedules.updateCounterPermissions', $schedule->id) }}";
            const getSeatLayoutUrlTemplate = "{{ route('admin.getSeatLayout', 'COUNTER_ID') }}";
            const saveBlockedSeatsUrl = "{{ route('admin.saveBlockedSeats') }}";

            // In-memory UI state
            let currentPermissions = []; // loaded from server
            let activeCounterId = null; // for seat grid / blocking modal
            let activeCounterBlockedSeats = []; // seat ids blocked for current counter
            let selectedSeatsForApply = []; // seat ids toggled on right grid

            // CSRF for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            const seatList = [
                "A1", "B1", "C1", "D1",
                "A2", "B2", "C2", "D2",
                "A3", "B3", "C3", "D3",
                "A4", "B4", "C4", "D4",
                "A5", "B5", "C5", "D5",
                "A6", "B6", "C6", "D6",
                "A7", "B7", "C7", "D7",
                "A8", "B8", "C8", "D8",
                "A9", "B9", "C9", "D9",
            ];

            // Utility: render seat grid (right panel) — change rows/columns as needed
            function renderSeatLayout() {
                const grid = $("#counter_seat_grid");
                grid.html("");

                seatList.forEach(seat => {
                    const isBlocked = blockedSeats.includes(seat);

                    const item = `
                                                                    <div class="seat-item border rounded p-2 text-center 
                                                                        ${isBlocked ? 'blocked-seat' : ''}"
                                                                        data-seat="${seat}"
                                                                        style="width:45px;cursor:pointer;">
                                                                        ${seat}
                                                                    </div>
                                                                `;

                    grid.append(item);
                });

                $(".seat-item").on("click", function () {
                    if ($(this).hasClass("blocked-seat")) return;  // already blocked → no change
                    $(this).toggleClass("active-seat");
                });
            }

            $(document).ready(function () {
                renderSeatLayout();
            });


            // Render Stations & Counters (left)
            function renderCounterPermissions(stations) {
                currentPermissions = stations || [];
                const $container = $('#route_station_permissions_container').empty();

                if (!stations || stations.length === 0) {
                    $container.html('<p class="text-muted">No counters assigned for this route.</p>');
                    return;
                }

                stations.forEach((station, idx) => {
                    const stationId = station.station_id;
                    const stationName = station.station_name;
                    const counters = station.counters || [];

                    // Accordion card
                    const card = $(`
                                                                                                                                <div class="card mb-2">
                                                                                                                                    <div class="card-body p-2 d-flex align-items-center gap-2">
                                                                                                                                        <div class="form-check ms-2">
                                                                                                                                            <input class="form-check-input station-check" type="checkbox" data-station-id="${stationId}" id="station_chk_${stationId}">
                                                                                                                                        </div>
                                                                                                                                        <strong class="ms-2">${stationName}</strong>
                                                                                                                                        <div class="ms-auto d-flex gap-2"></div>
                                                                                                                                    </div>
                                                                                                                                    <div class="list-group list-group-flush" id="station_counters_groups_${stationId}"></div>
                                                                                                                                </div>
                                                                                                                            `);

                    // list counters
                    counters.forEach(counter => {
                        const isAssigned = !!(counter.from_date && counter.to_date);
                        const counterId = counter.id;
                        const name = counter.name;
                        const fromDate = counter.from_date || '';
                        const toDate = counter.to_date || '';

                        const counterRow = $(`
                                                                                                                                    <div class="list-group-item d-flex align-items-center gap-3">
                                                                                                                                        <div class="form-check">
                                                                                                                                            <input class="form-check-input counter-permission-checkbox" type="checkbox" data-counter-id="${counterId}" data-counter-type="${counter.counter_type}" ${isAssigned ? 'checked' : ''}>
                                                                                                                                        </div>
                                                                                                                                        <div class="flex-grow-1">
                                                                                                                                            <div class="fw-bold">${name} <small class="text-muted">(${counter.counter_type})</small></div>
                                                                                                                                            <div class="d-flex gap-2 mt-1">
                                                                                                                                                <input type="date" class="form-control form-control-sm counter-from-date" value="${fromDate}">
                                                                                                                                                <input type="date" class="form-control form-control-sm counter-to-date" value="${toDate}">
                                                                                                                                                <button class="btn btn-sm btn-outline-secondary open-seat-block-btn" data-counter-id="${counterId}" data-counter-name="${name}">Block Seats</button>
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                `);

                        card.find(`#station_counters_groups_${stationId}`).append(counterRow);
                    });

                    $container.append(card);
                });

                // bind events
                bindLeftPanelEvents();
            }

            function bindLeftPanelEvents() {
                // Checkbox toggle => show/hide date inputs
                $('#route_station_permissions_container').off('change', '.counter-permission-checkbox')
                    .on('change', '.counter-permission-checkbox', function () {
                        const $row = $(this).closest('.list-group-item');
                        if ($(this).is(':checked')) {
                            $row.find('.counter-from-date').val($row.find('.counter-from-date').val() || new Date().toISOString().slice(0, 10));
                            $row.find('.counter-to-date').val($row.find('.counter-to-date').val() || '2099-12-31');
                        } else {
                            // optionally clear dates if unchecked:
                            // $row.find('.counter-from-date, .counter-to-date').val('');
                        }
                    });

                // Station-level check toggles all counters in station
                $('#route_station_permissions_container').off('change', '.station-check').on('change', '.station-check', function () {
                    const stationId = $(this).data('station-id');
                    const checked = $(this).is(':checked');
                    $(`#station_counters_groups_${stationId} .counter-permission-checkbox`).prop('checked', checked).trigger('change');
                });

                // open seat blocking modal
                $('#route_station_permissions_container').off('click', '.open-seat-block-btn')
                    .on('click', '.open-seat-block-btn', function () {
                        const counterId = $(this).data('counter-id');
                        const counterName = $(this).data('counter-name');
                        openSeatBlockingModal(counterId, counterName);
                    });
            }

            // Load from server
            function loadCounterPermissions() {
                $('#route_station_permissions_container').html('<p class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Loading...</p>');
                $.get(getCounterPermissionsUrl)
                    .done(function (res) {
                        if (res.status === 'success') {
                            renderCounterPermissions(res.stations || []);
                            // Optionally, preselect a counter to show its seats
                            const first = res.stations?.[0]?.counters?.[0];
                            if (first) {
                                activeCounterId = first.id;
                                // load blocked seats for first via seat layout call (or leave default)
                                loadSeatLayoutForCounter(activeCounterId);
                            } else {
                                renderSeatGrid(); // default grid
                            }
                        } else {
                            $('#route_station_permissions_container').html('<p class="text-danger">Failed to load data</p>');
                        }
                    })
                    .fail(function () {
                        $('#route_station_permissions_container').html('<p class="text-danger">Error fetching data</p>');
                    });
            }

            // Load seat layout and counter blocked seats for a given counter (for modal & right grid)
            function loadSeatLayoutForCounter(counterId) {
                if (!counterId) return;
                const url = getSeatLayoutUrlTemplate.replace('COUNTER_ID', counterId);
                $.get(url)
                    .done(function (res) {
                        if (res.status) {
                            activeCounterId = counterId;
                            activeCounterBlockedSeats = Array.isArray(res.counter_blocked_seats) ? res.counter_blocked_seats : (res.counter_blocked_seats || []);
                            // render right grid with blocked seats shown as selected
                            renderSeatGrid(res.layout?.pattern || res.layout || undefined, activeCounterBlockedSeats);
                            // reset selection buffer
                            selectedSeatsForApply = [...activeCounterBlockedSeats];
                        } else {
                            renderSeatGrid();
                        }
                    })
                    .fail(function () {
                        renderSeatGrid();
                    });
            }

            // open seat blocking modal (loads seat layout and opens)
            function openSeatBlockingModal(counterId, counterName) {
                $('#seatBlockingModalLabel').text('Block Seats for ' + counterName);
                $('#seatBlockingMapContainer').html('<p class="text-center text-muted">Loading seat map...</p>');
                $('#seatBlockingModal').modal('show');

                // fetch seat layout
                const url = getSeatLayoutUrlTemplate.replace('COUNTER_ID', counterId);
                $.get(url)
                    .done(function (res) {
                        if (res.status) {
                            // simple seat rendering inside modal
                            const layout = res.layout?.pattern || res.layout;
                            const blocked = Array.isArray(res.counter_blocked_seats) ? res.counter_blocked_seats : (res.counter_blocked_seats || []);
                            renderSeatMapInModal(layout, blocked);
                            // set selected counter
                            activeCounterId = counterId;
                            activeCounterBlockedSeats = blocked;
                        } else {
                            $('#seatBlockingMapContainer').html('<p class="text-danger">No seat map found</p>');
                        }
                    })
                    .fail(function () { $('#seatBlockingMapContainer').html('<p class="text-danger">Error loading seat map</p>'); });
            }

            function renderSeatMapInModal(pattern, blockedSeats) {
                const $container = $('#seatBlockingMapContainer').empty();
                // pattern expected as array of rows -> cell objects
                if (!pattern || !Array.isArray(pattern) || pattern.length === 0) {
                    // fallback: simple grid
                    const fallback = $('<div class="d-flex flex-wrap gap-2"></div>');
                    const letters = ['A', 'B', 'C', 'D'];
                    letters.forEach(L => {
                        for (let i = 1; i <= 9; i++) {
                            const id = L + i;
                            const btn = $(`<button class="btn btn-sm ${blockedSeats.includes(id) ? 'btn-danger' : 'btn-outline-secondary'} m-1" data-seat="${id}">${id}</button>`);
                            btn.on('click', function () {
                                $(this).toggleClass('btn-danger btn-outline-secondary');
                            });
                            fallback.append(btn);
                        }
                    });
                    $container.append(fallback);
                    return;
                }

                const wrapper = $('<div class="seat-map"></div>');
                pattern.forEach(row => {
                    const rowDiv = $('<div class="d-flex mb-2"></div>');
                    row.forEach(cell => {
                        if (!cell || cell.type === 'gap') {
                            rowDiv.append('<div style="width:40px"></div>');
                        } else {
                            const seatId = cell.id || cell.label;
                            const btn = $(`<button class="btn btn-sm ${blockedSeats.includes(seatId) ? 'btn-danger' : 'btn-outline-secondary'} m-1" data-seat="${seatId}">${seatId}</button>`);
                            btn.on('click', function () {
                                $(this).toggleClass('btn-danger btn-outline-secondary');
                            });
                            rowDiv.append(btn);
                        }
                    });
                    wrapper.append(rowDiv);
                });
                $container.append(wrapper);
            }

            // Save blocked seats from modal
            $('#saveBlockedSeatsBtn').on('click', function () {
                if (!activeCounterId) {
                    Swal.fire('Error', 'Counter not selected', 'error');
                    return;
                }

                // collect blocked seats from modal
                const blocked = [];
                $('#seatBlockingMapContainer').find('[data-seat]').each(function () {
                    const $b = $(this);
                    if ($b.hasClass('btn-danger')) blocked.push($b.data('seat'));
                });

                $.post(saveBlockedSeatsUrl, {
                    schedule_id: scheduleId,
                    counter_id: activeCounterId,
                    blocked_seats: blocked
                }).done(function (res) {
                    if (res.status === 'success') {
                        Swal.fire('Saved', res.message || 'Blocked seats updated', 'success');
                        $('#seatBlockingModal').modal('hide');
                        // reload right grid for this counter
                        loadSeatLayoutForCounter(activeCounterId);
                    } else {
                        Swal.fire('Error', res.message || 'Failed to save', 'error');
                    }
                }).fail(function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Request failed', 'error');
                });
            });

            // Top control button handlers
            $('#select_all_counters').on('click', function () { $('#route_station_permissions_container').find('.counter-permission-checkbox').prop('checked', true).trigger('change'); });
            $('#deselect_all_counters').on('click', function () { $('#route_station_permissions_container').find('.counter-permission-checkbox').prop('checked', false).trigger('change'); });
            $('#select_own_counters').on('click', function () { $('#route_station_permissions_container').find('.counter-permission-checkbox').each(function () { if ($(this).data('counter-type') === 'Own') $(this).prop('checked', true).trigger('change'); else $(this).prop('checked', false).trigger('change'); }); });
            $('#select_commission_counters').on('click', function () { $('#route_station_permissions_container').find('.counter-permission-checkbox').each(function () { if ($(this).data('counter-type') === 'Commission') $(this).prop('checked', true).trigger('change'); else $(this).prop('checked', false).trigger('change'); }); });

            // Apply selected seats (from right grid) to currently active counter
            $('#apply_selected_seats').on('click', function () {
                if (!activeCounterId) {
                    Swal.fire('Info', 'Select/open a counter first to apply seats', 'info');
                    return;
                }
                // apply selectedSeatsForApply array as blocked seats (or permitted seats based on your logic).
                // Here we'll call saveBlockedSeatsUrl to save the blocked seats for the counter.
                $.post(saveBlockedSeatsUrl, {
                    schedule_id: scheduleId,
                    counter_id: activeCounterId,
                    blocked_seats: selectedSeatsForApply
                }).done(function (res) {
                    if (res.status === 'success') {
                        Swal.fire('Saved', res.message || 'Blocked seats updated', 'success');
                        loadSeatLayoutForCounter(activeCounterId);
                    } else {
                        Swal.fire('Error', res.message || 'Failed to save', 'error');
                    }
                }).fail(function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Request failed', 'error');
                });
            });

            $('#clear_seat_selection').on('click', function () {
                selectedSeatsForApply = [];
                $('#counter_seat_grid').find('.seat-btn').removeClass('btn-success').addClass('btn-outline-secondary');
            });

            // Save all counter permissions (top-right save)
            $('#save_counter_permissions_btn').on('click', function () {
                // build payload
                const payload = [];
                $('#route_station_permissions_container .card').each(function () {
                    const stationId = $(this).find('.station-check').data('station-id') || null;
                    const counters = [];
                    $(this).find('.list-group-item').each(function () {
                        const cb = $(this).find('.counter-permission-checkbox');
                        const counterId = cb.data('counter-id');
                        const assigned = cb.prop('checked');
                        const from_date = $(this).find('.counter-from-date').val() || null;
                        const to_date = $(this).find('.counter-to-date').val() || null;
                        counters.push({
                            counter_id: counterId,
                            assigned: assigned,
                            from_date: from_date,
                            to_date: to_date
                        });
                    });
                    if (counters.length > 0) {
                        payload.push({
                            station_id: stationId,
                            counters: counters
                        });
                    }
                });

                // POST to server
                $.ajax({
                    url: updateCounterPermissionsUrl,
                    method: 'POST',
                    data: { data: payload },
                    beforeSend: function () { $('#save_counter_permissions_btn').prop('disabled', true).text('Saving...'); },
                    success: function (res) {
                        if (res.status === 'success') {
                            Swal.fire('Saved', res.message || 'Permissions updated', 'success');
                            // reload to refresh state
                            loadCounterPermissions();
                        } else {
                            Swal.fire('Error', res.message || 'Failed to save', 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Request failed', 'error');
                    },
                    complete: function () { $('#save_counter_permissions_btn').prop('disabled', false).text('Save Permissions'); }
                });
            });

            // initial load
            $(document).ready(function () {
                loadCounterPermissions();
            });

        })(jQuery);


    </script>

    {{-- 2. Load the external JS file containing the core functions --}}
    <script src="{{ asset('assets/js/schedule_update.js') }}"></script>


    {{-- 3. Initialization Logic --}}
    <script>
        $(document).ready(function () {

            // Tab 2 Form Bindings
            $('#counter-update-form-tab2').off('submit').on('submit', updateCounterPermissionTab2);
            $('#update_counter_tab2').off('click').on('click', updateCounterPermissionTab2);

            // Initial Tab Load Check (CRITICAL)
            const urlHash = window.location.hash;
            const initialTab = urlHash || '#kt_tab_pane_1';

            if (initialTab === '#kt_tab_pane_2') {
                // Renders the UI using the global routeStationList data
                setTimeout(renderStationCounterUI, 100);
            } else if (initialTab === '#kt_tab_pane_3') {
                if (typeof loadCounterPermissions === 'function') {
                    setTimeout(() => loadCounterPermissions(), 100);
                }
            }

            // Tab Change Event
            $('a[data-toggle="tab"]').off('shown.bs.tab').on('shown.bs.tab', function (e) {
                const target = $(e.target).attr('href');
                if (target === '#kt_tab_pane_2') {
                    setTimeout(() => renderStationCounterUI(), 100);
                } else if (target === '#kt_tab_pane_3') {
                    if (typeof loadCounterPermissions === 'function') {
                        setTimeout(() => loadCounterPermissions(), 100);
                    }
                }
            });

            // Input Change Handlers
            $(document).on('change', '#route_stations_div input, #route_stations_div select', function () {
                hasChanges = true;
                $('#update_counter_tab2').prop('disabled', false);
            });

            // Seat Blocking Save
            $(document).on('click', '#saveBlockedSeatsBtn', saveModalBlockedSeats);
        });
        let scheduleId = "{{ $schedule->id }}";

        // Load calendar
        function loadCalendar() {
            $.get(`/admin/schedules/${scheduleId}/calendar`, function (res) {
                $("#schedule_calendar").html(res.html);
            });
        }

        // Load On/Off list
        function loadOnOffList() {
            $.get(`/admin/schedules/${scheduleId}/onoff-list`, function (res) {
                $("#onoff_list_container").html(res.html);
            });
        }

        loadCalendar();
        loadOnOffList();


        // ❤️ FINAL WORKING MODAL OPEN
        $(document).on("click", "#btnOpenOnDayModal", function () {
            console.log("Modal Open Clicked"); // Debug
            let modal = new bootstrap.Modal(document.getElementById("onDayModal"));
            modal.show();
        });


        // Save On Days
        $(document).on("click", "#saveOnDay", function () {

            let data = {
                schedule_id: scheduleId,
                from_date: $("#from_date").val(),
                to_date: $("#to_date").val(),
                start_time: $("#start_time").val(),
                weekdays: $(".weekday:checked").map(function () {
                    return this.value;
                }).get(),
                _token: "{{ csrf_token() }}"
            };

            $.post(`/admin/schedules/on-days/save`, data, function (res) {
                if (res.status) {
                    let modal = bootstrap.Modal.getInstance(document.getElementById("onDayModal"));
                    modal.hide();
                    loadCalendar();
                    loadOnOffList();
                }
            });
        });


    </script>
@endsection -->