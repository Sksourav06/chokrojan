<div id="tripdetails-684601" class="tripdetails row border border-top-0 m-0 py-3 bg-success-o-40" style=""
    bis_skin_checked="1">
    <div class="col-sm-4" bis_skin_checked="1">
        <div class="row" bis_skin_checked="1">
            <div class="col-sm-12 col-12 pb-2" bis_skin_checked="1">
                <label for="station_from_to">Change (From Station ⟹ To Station)</label>
                <select name="station_from_to" id="station_from_to" class="form-control form-control-sm"
                    onchange="reloadSeatLayout();"> {{-- ✅ CRITICAL FIX: Add onchange event --}}
                    @forelse($stationFareList as $fare)
                        <option value="{{ $fare['value'] }}">
                            {{ $fare['text'] }}
                        </option>
                    @empty
                        <option value="" disabled selected>--- No Fares Found for this Route (Check Database) ---</option>
                    @endforelse

                </select>
            </div>
            <div class="col-sm-8 col-8 pl-4 pb-1" bis_skin_checked="1">
                <h3 class="pt-1 m-0 text-danger">
                    {{ $trip->name ?? 'N/A' }}
                </h3>

            </div>
            <div class="col-sm-4 col-4 pb-1 pl-0 text-center">
                <div class="col-12 p-0">
                    <button id="btn-refresh" class="btn btn-sm btn-pill btn-block bg-info text-white" {{-- FIX: Call the
                        correct, simplified JS function: reloadSeats() --}} onclick="reloadSeats();"
                        style="margin:0px 0px; padding:6px 2px;">

                        {{-- FIX: Remove 'fa-spin' class by default, JS will add it during loading --}}
                        <i class="fas fa-sync-alt fa-1x text-white pr-0"></i> Refresh
                    </button>
                </div>
            </div>
            <!-- <div class="col-sm-3 col-3 pr-0">
            <div class="row">
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-blocked mb-1 px-1 py-2">Blocked</button></div>
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-engaged mb-1 px-1 py-2">Engaged</button></div>
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-selected mb-1 px-1 py-2">Selected</button></div>
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-booked-male mb-1 px-1 py-2">Booked(M)</button></div>
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-booked-female mb-1 px-1 py-2">Booked(F)</button></div>
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-sold-male mb-1 px-1 py-2">Sold(M)</button></div>
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-sold-female mb-1 px-1 py-2">Sold(F)</button></div>
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-sold-online mb-1 px-1 py-2">Online</button></div>
                <div class="col-sm-12"><button class="btn btn-sm btn-block font-size-xs bg-vip mb-1 px-1 py-2">VIP</button></div>
                <div class="col-sm-12"><button id="btn-refresh" class="btn btn-sm btn-pill btn-block bg-info text-white" onclick="updateShowingTripSeatplan();" style="margin:30px 0px; padding:6px 2px;"><i class="fas fa-sync-alt fa-1x text-white pr-0"></i> Refresh</button></div>
            </div>
        </div>-->
            <div class="col-sm-12 col-12" id="seatplan-layout-div">
                @php
                    // ===============================
                    // 1. Core Variables
                    // ===============================
                    $layout = $trip->seat_layout;
                    $rows = $layout->rows ?? 4;
                    $cols = $layout->columns ?? 4;

                    $routeSequencesArray = $trip->route->routeStationSequences
                        ->pluck('sequence_order', 'station_id')
                        ->toArray();

                    $userFromSeq = $routeSequencesArray[$originStationId] ?? 1;
                    $userToSeq = $routeSequencesArray[$destinationStationId] ?? 99;

                    // ===============================
                    // 2. Filter Conflicting Tickets
                    // ===============================
                    $conflictingTickets = $tickets->filter(function ($ticket) use ($routeSequencesArray, $userFromSeq, $userToSeq) {
                        $ticketFromSeq = $routeSequencesArray[$ticket->from_station_id] ?? 1;
                        $ticketToSeq = $routeSequencesArray[$ticket->to_station_id] ?? 99;

                        // Segment overlap logic
                        return $ticketFromSeq < $userToSeq && $ticketToSeq > $userFromSeq;
                    });
                @endphp

                <div id="seatplan-div" class="rounded bg-white p-1"
                    style="border:1px dotted #CCCCCC; width: 100%; min-height:175px;">
                    @for($r = 1; $r <= $rows; $r++)
                        <div class="seat-row d-flex justify-content-center">
                            @for($c = 1; $c <= $cols; $c++)
                                @php
                                    $seat = chr(64 + $r) . $c;

                                    // Check conflicting ticket for this seat
                                    $conflictingTicket = $conflictingTickets->firstWhere(function ($ticket) use ($seat) {
                                        $seatNumbers = explode(',', $ticket->seat_numbers ?? '');
                                        return in_array($seat, $seatNumbers);
                                    });

                                    $engagedLock = $activeLocks->firstWhere('seat_number', $seat) ?? null;

                                    $seatClass = 'btn-outline-primary';
                                    $tooltip = "{$seat} Available";
                                    $onClickAction = 'onclick="seatSelect(this)"';

                                    if ($conflictingTicket) {
                                        $customerName = $conflictingTicket->customer_name ?? 'N/A';
                                        // ✅ FIX: Use Nullsafe Operator (?->) to safely access 'name'
                                        $counterName = $conflictingTicket->issueCounter?->name ?? 'N/A';
                                        $tooltip = "{$customerName} <br>Sold from {$counterName}";

                                        if ($conflictingTicket->status_label === 'Booked') {
                                            $seatClass = $conflictingTicket->passenger_gender === 'female' ? 'bg-booked-female' : 'bg-booked-male';
                                        } elseif ($conflictingTicket->status_label === 'Sold') {
                                            $seatClass = $conflictingTicket->passenger_gender === 'female' ? 'bg-sold-female' : 'bg-sold-male';
                                        }

                                        $onClickAction = 'onclick="openSoldTicketModal(' . $conflictingTicket->id . ')"';
                                    } elseif ($engagedLock) {
                                        // ✅ FIX: Use Nullsafe Operator (?->) to safely access 'name'
                                        $counterName = $engagedLock->counter?->name ?? 'Unknown Counter';
                                        $expiryTime = \Carbon\Carbon::parse($engagedLock->expires_at)->format('h:i A');
                                        $tooltip = "Locked by {$counterName}<br>Expires at {$expiryTime}";
                                        $seatClass = 'bg-engaged';
                                        $onClickAction = '';
                                    }
                                @endphp

                                <div class="bsp bsp-5 p-1">
                                    <button type="button" id="{{ $seat }}"
                                        class="seatBtn seatInfo btn btn-sm btn-block px-1 py-1 my-0 {{ $seatClass }}"
                                        data-toggle="tooltip" data-html="true" title="{!! $tooltip !!}" data-seat="{{ $seat }}"
                                        {!! $onClickAction !!}>
                                        {{ $seat }}
                                    </button>
                                </div>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                @if($cols > 2 && $c == floor($cols / 2))
                                    <div class="bus-aisle-gap mx-6"></div>
                                @endif
                            @endfor
                        </div>
                    @endfor
                </div>
                <div class="modal fade" id="soldTicketModal">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">
                                    {{-- Route Name (Confirmed to use 'name') --}}
                                    {{ $trip->route->name ?? 'N/A' }}

                                    |

                                    {{-- Coach Number (Confirmed to use 'registration_number') --}}
                                    {{ $ticket->schedule?->name ?? 'N/A' }}

                                    |

                                    {{-- Date, Time (Confirmed to use start_time) --}}
                                    {{ date('d M Y, h:i A', strtotime($ticket->schedule?->start_time ?? now())) }}
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>

                            <div class="modal-body" id="soldTicketModalBody">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4" bis_skin_checked="1">
        <div class="row" bis_skin_checked="1">
            <div class="col-sm-12" bis_skin_checked="1">
                <div class="row" bis_skin_checked="1">
                    <div class="form-group col-sm-7 col-7 pr-1" bis_skin_checked="1">
                        <label for="boarding_counter_id"><i class="far fa-star text-danger fa-sm" title=""
                                data-toggle="tooltip" data-placement="top" data-original-title="Required"></i>
                            Boarding
                            Counter</label>
                        <select name="boarding_counter_id" id="boarding_counter_id"
                            class="form-control form-control-sm">
                            <option value="">Select Boarding Counter...</option>

                            @foreach($boardingCounters as $counter)
                                <option value="{{ $counter->id }}">{{ $counter->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-5 col-5 pl-1" bis_skin_checked="1">
                        <label for="boarding_place">Boarding Place</label>
                        <input type="text" class="form-control form-control-sm" id="boarding_place"
                            name="boarding_place" value="">
                    </div>
                </div>
                <div class="row" bis_skin_checked="1">
                    <div class="form-group col-sm-7 col-7 pr-1" bis_skin_checked="1">
                        <label for="dropping_counter_id"><i class="far fa-star text-danger fa-sm" title=""
                                data-toggle="tooltip" data-placement="top" data-original-title="Required"></i>
                            Dropping
                            Counter</label>
                        <select name="dropping_counter_id" id="dropping_counter_id"
                            class="form-control form-control-sm">
                            <option value="">Select Dropping Counter...</option>

                            @foreach($droppingCounters as $counter)
                                <option value="{{ $counter->id }}">{{ $counter->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-5 col-5 pl-1" bis_skin_checked="1">
                        <label for="dropping_place">Dropping Place</label>
                        <input type="text" class="form-control form-control-sm" id="dropping_place"
                            name="dropping_place" value="">
                    </div>
                </div>
                <div class="row" bis_skin_checked="1">
                    <div class="row" bis_skin_checked="1">
                        <div class="form-group col-sm-12" bis_skin_checked="1">
                            <table class="table table-sm table-bordered bg-white mb-0">
                                <tbody id="sell-seat-table">
                                    <tr>
                                        <th width="10%" style="text-align:center">#</th>
                                        <th width="20%" style="text-align:left">SEAT</th>
                                        <th width="40%" style="text-align:left">TYPE</th>
                                        <th width="30%" style="text-align:right">FARE</th>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td style="text-align:right" colspan="3">Sub Total :</td>
                                        <td style="text-align:right" id="sub-total">0.00</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:right" colspan="3">Goods Charge / Extra Fare : ⊕</td>
                                        <td style="text-align:right">
                                            <input type="text" class="form-control form-control-sm" id="goods-charge"
                                                value="0" onkeyup="addGoodsCharge(this.value)" style="height: 25px;">
                                        </td>
                                    </tr>
                                    @if(auth()->user()->can('Can Set Discount'))
                                        <tr>
                                            <td style="text-align:right" colspan="3">Discount : ⊝</td>
                                            <td style="text-align:right">
                                                <input type="text" class="form-control form-control-sm" id="discount-amount"
                                                    value="0" onkeyup="subDiscountAmount(this.value)" style="height: 25px;">
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td style="text-align:right" colspan="3">Callerman Commission : ⊝</td>
                                        <td style="text-align:right">
                                            <input type="text" class="form-control form-control-sm"
                                                id="callerman-commission" value="0"
                                                onkeyup="setCallermanCommissionAmount(this.value)"
                                                style="height: 25px;">
                                        </td>
                                    </tr>
                                    <tr id="callerman_mobile_tr" style="display: none;">
                                        <td style="text-align:right" colspan="3">Callerman Mobile No. : </td>
                                        <td style="text-align:right">
                                            <input type="text" class="form-control form-control-sm"
                                                id="callerman-mobile" value=""
                                                onkeyup="setCallermanMobileNumber(this.value)" style="height: 25px;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:left" colspan="2">
                                            Total Seat : <span id="total-seat">0</span>
                                        </td>
                                        <td style="text-align:right">Grand Total :</td>
                                        <td style="text-align:right" id="grand-total">0.00</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row px-3 pb-3" bis_skin_checked="1">
                    <div class="col-3 p-0 pl-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-booked-male mb-1 px-1 py-1">Booked(M)</button>
                    </div>
                    <div class="col-3 p-0 pl-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-booked-female mb-1 px-1 py-1">Booked(F)</button>
                    </div>
                    <div class="col-3 p-0 pl-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-sold-male mb-1 px-1 py-1">Sold(M)</button>
                    </div>
                    <div class="col-3 p-0 px-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-sold-female mb-1 px-1 py-1">Sold(F)</button>
                    </div>
                    <div class="col-3 p-0 pl-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-blocked mb-1 px-1 py-1">Blocked</button>
                    </div>
                    <div class="col-3 p-0 pl-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-engaged mb-1 px-1 py-1">Engaged</button>
                    </div>
                    <div class="col-3 p-0 pl-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-selected mb-1 px-1 py-1">Selected</button>
                    </div>
                    <div class="col-2 p-0 pl-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-sold-online mb-1 px-1 py-1">Online</button>
                    </div>
                    <div class="col-1 p-0 px-1" bis_skin_checked="1"><button
                            class="btn btn-sm btn-block font-size-xs bg-vip mb-1 px-1 py-1">VIP</button></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4" bis_skin_checked="1">
        <div class="row" bis_skin_checked="1">
            <div class="col-sm-12" bis_skin_checked="1">
                <div class="row" bis_skin_checked="1">
                    <div class="form-group col-sm-12">
                        <label for="passenger_mobile">
                            Passenger Mobile Number <span id="number-check"></span>
                        </label>
                        <input type="text" class="form-control form-control-sm" id="passenger_mobile"
                            name="passenger_mobile" maxlength="11" onkeyup="getPassengerInfoByMobile(this.value)">
                    </div>

                    <div class="form-group col-sm-12">
                        <label for="passenger_name">Passenger Full Name</label>
                        <input type="text" class="form-control form-control-sm" id="passenger_name"
                            name="passenger_name" value="">
                    </div>

                    <div class="form-group col-sm-6 col-6 pr-1" bis_skin_checked="1">
                        <select name="passenger_gender" id="passenger_gender" class="form-control form-control-sm">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-6 col-6 pl-1" bis_skin_checked="1">
                        <select name="passenger_nationality" id="passenger_nationality"
                            class="form-control form-control-sm">

                        </select>
                    </div>
                    <div class="form-group col-sm-6 col-6 pr-1" bis_skin_checked="1">
                        <input type="text" class="form-control form-control-sm" id="passenger_email"
                            name="passenger_email" placeholder="Email Address" value="">
                    </div>
                    <div class="form-group col-sm-6 col-6 pl-1" bis_skin_checked="1">
                        <input type="text" class="form-control form-control-sm" id="passenger_passport"
                            name="passenger_passport" placeholder="Passport Number" value="">
                    </div>
                </div>
                <div class="row" bis_skin_checked="1">
                    <div class="form-group col-sm-6 col-6 pr-1" bis_skin_checked="1">
                        <div class="input-icon" bis_skin_checked="1">
                            <input type="text" class="form-control form-control-sm" id="keep_book_until"
                                name="keep_book_until" placeholder="Keep booking until" value="">
                            <span><i class="fas fa-calendar-alt text-muted"></i></span>
                        </div>
                    </div>
                    <div class="form-group col-sm-6 col-6 pl-1" bis_skin_checked="1">
                        @if(auth()->user()->can('Can Book Vip Ticket'))
                            <div class="checkbox-inline rounded bg-white"
                                style="border: 1px solid #E5EAEE; padding: 0.45rem 0.75rem;" bis_skin_checked="1">
                                <label class="checkbox checkbox-outline checkbox-success">
                                    <input type="checkbox" name="vip_seat" id="vip_seat">
                                    <span></span>
                                    VIP Passenger
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-6 col-6 pr-1">
                        <select name="def_counter_id" id="def_counter_id" class="form-control form-control-sm">
                            <option value="">Select Counter...</option>
                            @foreach($counters as $counter)
                                <option value="{{ $counter->id }}" {{ $user->counter_id == $counter->id ? 'selected' : '' }}>
                                    {{ $counter->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                    <div class="form-group col-sm-6 col-6 pl-1">
                        <span id="search-counter-master" style="position: absolute; left: 15px; top: 8px;"></span>
                        <select name="def_counter_master_id" id="def_counter_master_id"
                            class="form-control form-control-sm">
                            <option value="">Select Master...</option>
                        </select>
                    </div>
                </div>
                <div class="row" bis_skin_checked="1">
                    <div class="form-group col-sm-12" align="center" bis_skin_checked="1">
                        <i class="far fa-clock text-danger fa-2x"> <span id="ticket-timer"></span></i>
                    </div>
                    <div class="form-group col-sm-12" align="center" bis_skin_checked="1">
                        <input type="hidden" name="count_comm" id="count_comm" value="0">

                        {{-- Seat Book Button (Mode 0) --}}
                        @if(auth()->user()->can('Can Book Ticket'))
                            <button id="confirm0" name="confirm" class="btn btn-sm btn-pill btn-warning"
                                style="min-width: 40%;" value="Seat Book" onclick="confirmTicketIssue(0);">Seat
                        Book</button>@endif

                        {{-- Seat Sell Button (Mode 1) --}}
                        @if(auth()->user()->can('Can Issue Ticket'))
                            <button id="confirm1" name="confirm" class="btn btn-sm btn-pill btn-danger"
                                style="min-width: 40%;" value="Seat Sell" onclick="confirmTicketIssue(1);">Seat
                        Sell</button>@endif

                        <form id="ticket-issue-form" method="POST" action="{{ route('admin.ticket.issue.store') }}"
                            style="display:none;">
                            @csrf

                            <input type="hidden" name="from_station_id" id="from_station_id_submit">
                            <input type="hidden" name="to_station_id" id="to_station_id_submit">

                            <input type="hidden" name="boarding_counter_id" id="boarding_counter_id_submit">
                            <input type="hidden" name="dropping_counter_id" id="dropping_counter_id_submit">

                            <input type="hidden" name="customer_mobile" id="passenger_mobile_submit">
                            <input type="hidden" name="customer_name" id="passenger_name_submit">
                            <input type="hidden" name="passenger_email" id="passenger_email_submit">
                            <input type="hidden" name="journey_date" id="journey_date_hidden"
                                value="{{ $schedule->journey_date ?? date('Y-m-d') }}">
                            <input type="hidden" name="grand_total" id="grand_total_submit">
                            <input type="hidden" name="selected_seats" id="selected_seats">
                            <input type="hidden" name="def_counter_id" id="def_counter_id_hidden">
                            <input type="hidden" name="def_counter_master_id" id="def_counter_master_id_hidden">
                            <input type="hidden" name="schedule_id" id="schedule_id" value="{{ $trip->id }}">
                            <input type="hidden" name="selected_seats" id="selected_seats">
                            <input type="hidden" name="issued_by" value="{{ auth()->id() }}">
                        </form>


                    </div>
                    <div class="form-group col-sm-12" align="center" bis_skin_checked="1">
                        <button type="button" class="btn btn-sm btn-pill btn-info" style="min-width: 40%;" {{-- FIX: Use
                            Blade syntax for dynamic ID --}} onclick="showTripSheet({{ $trip->id }});">
                            Trip Sheet
                        </button>
                        <input type="button" name="reset" class="btn btn-sm btn-pill btn-gray-light"
                            style="min-width: 40%;" value="Reset" onclick="resetSeatIssueForm();">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        /* Add this CSS block to fix the jitter/flicker on hover 
    This assumes available seats use btn-outline-primary class structure.
*/
        .seatBtn.btn-outline-primary:hover {
            /* Ensures smooth color transition */
            transition: background-color 0.15s ease-in-out,
                border-color 0.15s ease-in-out;

            /* Important: Prevent Chrome/Safari's default focus outline jitter */
            outline: none !important;
            box-shadow: none !important;
        }

        /* If all seats use a custom size and you want to ensure the position/size doesn't jump
    (which can happen if box-shadow changes drastically on hover):
*/
        .bsp .seatBtn {
            /* Set fixed height/width to prevent layout shift */
            width: 100%;
            height: 35px;
            /* Adjust as needed */
            line-height: 1.5;
        }

        /* Engaged Seat Style (Warning/Orange color suggested) */


        /* যদি আপনি গাঢ় নীল রঙ চান, যা আপনার ছবিতে আছে: */
        .bg-engaged.btn-primary {
            background-color: #007bff !important;
            /* Standard Primary Blue */
            border-color: #007bff !important;
            color: #ffffff !important;
        }
    </style>
    <script src="{{ asset('js/ticket-issue.js') }}"></script>
</div>
@php
    $settings = \App\Models\SystemSetting::first();
@endphp
<script>
    // ===================================================================
    // 1. GLOBAL VARIABLE INITIALIZATION (Loaded from Controller/Blade)
    // ===================================================================
    window.MAX_SEAT_LIMIT = @json($settings->counter_max_seat_per_ticket);
    // Core Trip Data
    const TRIP_ID = {{ $trip->id ?? 'null' }};
    const SEAT_FARE = {{ $displayFare ?? 0 }};

    // Seat Tracking
    let selectedSeats = []; // Array to hold currently selected seat numbers
    // let selectedSeats = [];
    let currentFare = 0;
    // Calculation Inputs (These need to be global to be updated by onkeyup handlers)
    let currentGoodsCharge = 0;
    let currentDiscountAmount = 0;
    let currentCallermanCommission = 0;
    let lockExpirationTime = null; // ✅ NEW: Track when the user's current lock expires
    let countdownInterval = null;

    // ===================================================================
    // 2. CALCULATION FUNCTIONS
    // ===================================================================

    // Function to calculate and update Sub Total and Grand Total
    function parseFare() {
        const val = $("#station_from_to").val();

        // If the station selection input is empty, use the last known currentFare, 
        // otherwise default to 0.
        if (!val) {
            return currentFare || 0;
        }

        const p = val.split(",");
        // Ensure the fare part (index 2) is numeric
        const fare = parseFloat(p[2]) || 0;

        // Update the global currentFare for stability
        currentFare = fare;
        return fare;
    }


    // -------------------------
    // Grand Total Calculator (Fixed for robust reading of input fields)
    // -------------------------
    function calcGrand() {
        const fare = parseFare();
        const subtotal = selectedSeats.length * fare;

        $("#sub-total").text(subtotal.toFixed(2));

        let total = subtotal;

        // FIX: Read input values robustly by ensuring they are clean numbers.
        const goodsChargeVal = $("#goods-charge").val() || '0';
        const discountVal = $("#discount-amount").val() || '0';
        const commissionVal = $("#callerman-commission").val() || '0';

        // Parse the values, replacing commas if necessary
        total += parseFloat(goodsChargeVal.replace(/,/g, '')) || 0;
        total -= parseFloat(discountVal.replace(/,/g, '')) || 0;
        total -= parseFloat(commissionVal.replace(/,/g, '')) || 0;

        if (total < 0) total = 0;

        $("#grand-total").text(total.toFixed(2));
    }


    // Expose to HTML (These handlers already call calcGrand on keyup)
    window.addGoodsCharge = calcGrand;
    window.subDiscountAmount = calcGrand;
    window.DB_BUS_TYPE = "{{ $trip->bus_type ?? $schedule->bus_type ?? 'Standard' }}";
    window.setCallermanCommissionAmount = calcGrand;
    window.setCallermanMobileNumber = function () { /* Does nothing to total */ };

    // ===================================================================
    // 3. UI UPDATE & SEAT LOCKING LOGIC
    // ===================================================================

    // Function to update the local UI and counts after server confirmation
    function updateSeatUI(btn, seat, isSelected) {
        const $btn = $(btn);

        if (isSelected) {
            // Select logic
            $btn.removeClass("btn-outline-primary").addClass("btn-success bg-selected");
            if (!selectedSeats.includes(seat)) {
                selectedSeats.push(seat);
            }
        } else {
            // Deselect logic
            selectedSeats = selectedSeats.filter(s => s !== seat);
            $btn.removeClass("btn-success bg-selected").addClass("btn-outline-primary");
        }

        // Core counting and calculation update
        $("#total-seat").text(selectedSeats.length);
        if (typeof calcGrand === 'function') {
            calcGrand();
        }
    }


    // Main function called when a seat is clicked
    // -------------------------
    // TIMER LOGIC
    // -------------------------

    // ✅ NEW: Starts the countdown timer
    // ... (code before startLockTimer) ...

    function startLockTimer() {
        // If a timer is already running, clear it first
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }

        // If no seats are selected, don't start the timer
        if (selectedSeats.length === 0) {
            stopLockTimer();
            return;
        }

        // ✅ FIX: Use the dynamic LOCK_LIFETIME_SECONDS (multiplied by 1000 for milliseconds)
        lockExpirationTime = Date.now() + (LOCK_LIFETIME_SECONDS * 1000);

        // Update the timer display every second
        countdownInterval = setInterval(updateTimerDisplay, 1000);
        updateTimerDisplay(); // Initial display update
    }

    // ✅ NEW: Stops and resets the countdown timer (Unchanged, already correct)
    function stopLockTimer() {
        clearInterval(countdownInterval);
        countdownInterval = null;
        lockExpirationTime = null;
        $("#ticket-timer").text("");
    }

    // ✅ NEW: Updates the display every second (Unchanged, already correct)
    function updateTimerDisplay() {
        if (!lockExpirationTime || selectedSeats.length === 0) {
            stopLockTimer();
            return;
        }

        const remainingTime = lockExpirationTime - Date.now();

        if (remainingTime <= 0) {
            // Time expired! Stop timer and notify user (optional: clear selected seats)
            stopLockTimer();

            // This is where you might want to alert the user their lock expired
            if (selectedSeats.length > 0) {
                // Note: Server-side cron job will handle the actual database unlock.
                // This is just a client-side notification/UI reset.
                alert("Your seat lock has expired! Please select seats again.");
            }

            // Soft reset selected seats and UI (Server sync will correct the colors)
            selectedSeats = [];
            calcGrand();
            syncSeatLocks(); // Force a sync to update UI colors immediately

            return;
        }

        const minutes = Math.floor(remainingTime / 60000);
        const seconds = Math.floor((remainingTime % 60000) / 1000);

        const formattedTime =
            minutes.toString().padStart(2, '0') + ':' +
            seconds.toString().padStart(2, '0');

        $("#ticket-timer").text(formattedTime);
    }

    // -------------------------
    // Load modal for sold ticket
    // -------------------------
    window.openSoldTicketModal = function (ticketId) {

        $("#soldTicketModalBody").html("Loading...");

        $.get(`/admin/ticket-issue/view/${ticketId}`, function (html) {
            $("#soldTicketModalBody").html(html);
            $("#soldTicketModal").modal("show");
        });
    };


    // -------------------------
    // TIMER & LOCK EVENT HOOKS
    // -------------------------
    // Main function called when a seat is clicked
    // window.seatSelect = function (btn) {
    //     const $btn = $(btn);
    //     const seat = $btn.data("seat");

    //     // CRITICAL FIX: Prevent execution if already disabled (Anti-Race Condition)
    //     if ($btn.prop('disabled')) return;

    //     if (!seat || !TRIP_ID) return;

    //     // 1. Permanent/Engaged Status Checks
    //     if ($btn.hasClass("bg-sold-male") || $btn.hasClass("bg-sold-female") || $btn.hasClass("bg-booked-male") || $btn.hasClass("bg-booked-female")) {
    //         return;
    //     }
    //     if ($btn.hasClass("bg-engaged")) {
    //         alert("Seat is temporarily locked by another counter.");
    //         return;
    //     }

    //     // Disable button immediately while AJAX runs
    //     $btn.prop('disabled', true);

    //     const requestData = {
    //         _token: $("input[name=_token]").val(), // CSRF Token
    //         schedule_id: TRIP_ID,
    //         seat_number: seat
    //     };

    //     let ajaxRequest;

    //     if (selectedSeats.includes(seat)) {
    //         // DESELECT - Release the lock on the server
    //         ajaxRequest = $.post('/admin/seat/release', requestData)
    //             .done(function () {
    //                 updateSeatUI(btn, seat, false); // Successful release

    //                 // If all seats are deselected, stop the timer
    //                 if (selectedSeats.length === 0) {
    //                     stopLockTimer();
    //                 }
    //             })
    //             .fail(function (xhr) {
    //                 alert("Could not release seat lock.");
    //             });
    //     } else {
    //         // SELECT - Engage the lock on the server
    //         ajaxRequest = $.post('/admin/seat/engage', requestData)
    //             .done(function (response) {
    //                 if (response.success) {
    //                     updateSeatUI(btn, seat, true); // Successful lock
    //                     startLockTimer(); // ✅ NEW: Start/Reset the timer upon successful lock
    //                 } else {
    //                     alert(response.message || "Failed to lock seat.");
    //                 }
    //             })
    //             .fail(function (xhr) {
    //                 const response = xhr.responseJSON;
    //                 alert("An error occurred while locking the seat.");
    //             });
    //     }

    //     // CRITICAL FIX: Always re-enable the button after the AJAX request is complete
    //     ajaxRequest.always(function () {
    //         $btn.prop('disabled', false);
    //     });
    // };


    // -------------------------
    // INIT
    // -------------------------
    $(function () {
        // Initialize currentFare using the parsed value on load
        currentFare = parseFare();

        // Initial calculation to set 0.00 on all footer fields
        calcGrand();

        $('[data-toggle="tooltip"]').tooltip();
    });


    // -------------------------
    // Load modal for sold ticket
    // -------------------------
    window.openSoldTicketModal = function (ticketId) {

        $("#soldTicketModalBody").html("Loading...");

        $.get(`/admin/ticket-issue/view/${ticketId}`, function (html) {
            $("#soldTicketModalBody").html(html);
            $("#soldTicketModal").modal("show");
        });
    };


    // -------------------------
    // TIMER & LOCK EVENT HOOKS
    // -------------------------
    // Main function called when a seat is clicked
    // (This is the repeated function from above, kept for completeness)
    // window.seatSelect definition remains here for code structure integrity.

    // 4. Sync Seat Locks Function (For real-time visibility from other counters)
    function syncSeatLocks() {
        if (!TRIP_ID) return;

        $.get(`/admin/seats/locks/${TRIP_ID}`).done(function (locks) {
            // Reset existing engagement styles (unless sold/booked or currently selected)
            $('.seatBtn').not('.bg-sold-male, .bg-sold-female, .bg-booked-male, .bg-booked-female, .bg-selected').removeClass('bg-engaged btn-danger').addClass('btn-outline-primary');

            // Apply new engagement styles
            locks.forEach(function (lock) {
                const $seatBtn = $('[data-seat="' + lock.seat_number + '"]');

                // Apply engaged class only if it's NOT the currently selected seat
                if (!$seatBtn.hasClass('bg-selected')) {
                    $seatBtn.removeClass('btn-outline-primary').addClass('bg-engaged');
                }
            });
        });
    }

    // Sync locks every 30 seconds
    setInterval(syncSeatLocks, 30000);

    // Open Sold Ticket Modal
    window.openSoldTicketModal = function (ticketId) {

        $("#soldTicketModalBody").html("<div class='text-center p-3'>Loading...</div>");

        $.ajax({
            url: "/admin/ticket-issue/view/" + ticketId,
            type: "GET",
            success: function (res) {

                console.log("Modal Load:", res);

                if (res.status) {
                    $("#soldTicketModalBody").html(res.html);
                    $("#soldTicketModal").modal("show");
                } else {
                    Swal.fire("Error", res.message, "error");
                }
            },
            error: function () {
                Swal.fire("Error", "Failed to load ticket", "error");
            }
        });

    };



    // Cancel ticket
    window.cancelTicket = function (ticketId) {
        Swal.fire({
            title: "Are you sure?",
            text: "Are you sure you want to cancel this ticket?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#1BC5BD',
            confirmButtonText: "Yes,!",
            cancelButtonText: "No"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/ticket-issue/cancel/' + ticketId,
                    type: 'POST',
                    data: {
                        _token: $("meta[name=csrf-token]").attr("content")
                    },
                    beforeSend: function () {
                        // প্রসেসিং চলাকালীন বাটন বা পপআপ লোডার দেখানো
                        Swal.fire({
                            title: 'Cancelling...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading() }
                        });
                    },
                    success: function (res) {
                        if (res.status) {
                            Swal.fire("Cancelled!", res.message, "success");

                            // ১. মডালটি বন্ধ করা
                            $("#soldTicketModal").modal("hide");

                            // ২. 🚨 আপনার reloadSeats ফাংশনটি কল করা (যাতে সীট লেআউট আপডেট হয়)
                            if (typeof window.reloadSeats === "function") {
                                window.reloadSeats();
                            }
                        } else {
                            Swal.fire("Error", res.message, "error");
                        }
                    },
                    error: function (xhr) {
                        Swal.fire("Error", "Internal Server Error. Please try again.", "error");
                    }
                });
            }
        });
    };

    // Global function to load Trip Sheet
    window.showTripSheet = function (tripId) {

        // Show loading indicator if needed
        // Swal.fire({ title: 'Loading Trip Sheet...', didOpen: () => { Swal.showLoading() } });

        $.ajax({
            url: "/admin/ticket-issue/trip-sheet/" + tripId, // Ensure this route exists in web.php
            type: "GET",
            success: function (res) {
                if (res.status) {
                    // Swal.close();

                    // Ensure a modal container exists
                    if ($('#tripSheetModal').length === 0) {
                        $('body').append(`
                        <div class="modal fade" id="tripSheetModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document" style="max-width: 1100px;">
                                <div class="modal-content" id="tripSheetModalContent"></div>
                            </div>
                        </div>
                    `);
                    }

                    // Load content and show modal
                    $('#tripSheetModalContent').html(res.html);
                    $('#tripSheetModal').modal('show');
                } else {
                    alert(res.message);
                }
            },
            error: function (xhr) {
                alert('Error loading trip sheet: ' + xhr.statusText);
            }
        });
    }

    function getPassengerInfoByMobile(mobile) {
        if (mobile.length < 11) return; // পূর্ণ নম্বর না হলে skip

        $.ajax({
            url: '/admin/get-passenger-info/' + mobile,
            type: 'GET',
            success: function (response) {
                if (response.status === 'success') {
                    $('#passenger_name').val(response.name);
                } else {
                    $('#passenger_name').val('');
                }
            },
            error: function () {
                $('#passenger_name').val('');
            }
        });
    }

    // let selectedSeatsArray = [];
    // let maxLimit = parseInt($('#max_seat_limit').val()); // ডেটাবেস থেকে আসা লিমিট

    // $('.seat-btn').on('click', function () {
    //     let seatNumber = $(this).data('seat-no');

    //     // চেক করা হচ্ছে সিটটি কি অলরেডি সিলেক্টেড কি না
    //     if ($(this).hasClass('selected')) {
    //         // আন-সিলেক্ট করা
    //         $(this).removeClass('selected');
    //         selectedSeatsArray = selectedSeatsArray.filter(item => item !== seatNumber);
    //     } else {
    //         // নতুন সিট সিলেক্ট করার আগে লিমিট চেক
    //         if (selectedSeatsArray.length >= maxLimit) {
    //             alert("দুঃখিত! আপনি একবারে " + maxLimit + "টির বেশি সিট বুক করতে পারবেন না।");
    //             return; // এখানেই কোড থেমে যাবে, সিট সিলেক্ট হবে না
    //         }

    //         $(this).addClass('selected');
    //         selectedSeatsArray.push(seatNumber);
    //     }

    //     // আপনার হিডেন ফিল্ডে সিটগুলো কমা দিয়ে আপডেট করা
    //     $('#selected_seats').val(selectedSeatsArray.join(','));

    //     // গ্র্যান্ড টোটাল আপডেট করার লজিক এখানে থাকতে পারে
    //     updateGrandTotal();
    // });


    $(document).ready(function () {
        $('#def_counter_id').on('change', function () {
            let counterId = $(this).val();
            let masterSelect = $('#def_counter_master_id');
            masterSelect.empty().append('<option value="">Loading...</option>');

            if (counterId) {
                $.ajax({
                    url: '/admin/counter/' + counterId + '/users', // route pointing to getCounterUsers
                    method: 'GET',
                    success: function (users) {
                        masterSelect.empty().append('<option value="">Select Master...</option>');
                        users.forEach(function (user) {
                            masterSelect.append('<option value="' + user.id + '">' + user.name + '</option>');
                        });
                    },
                    error: function () {
                        masterSelect.empty().append('<option value="">Failed to load users</option>');
                    }
                });
            } else {
                masterSelect.empty().append('<option value="">Select Master...</option>');
            }
        });
    });
</script>