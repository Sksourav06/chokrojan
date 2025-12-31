<div id="tripSheetModalContent" class="modal-content">
    <style>
        .table-bordered th,
        .table-bordered td {
            padding: 0.5em;
            font-size: 11px;
        }

        .text-red {
            color: red;
            font-weight: bold;
        }

        .text-left {
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .only-print {
                display: block !important;
            }

            .modal-footer {
                display: none;
            }

            .table-responsive-lg {
                overflow-x: visible !important;
            }
        }
    </style>

    {{-- Modal Header --}}
    <div class="modal-header">
        <h5 class="modal-title">
            <b>TRIP SHEET</b> &nbsp;&nbsp;
            {{ $trip->route->name ?? 'N/A' }} |
            {{ $trip->bus->registration_number ?? 'N/A' }} |
            {{ date('d M Y, h:i A', strtotime($trip->start_time)) }}
        </h5>
        <button type="button" class="close" data-dismiss="modal">
            <i class="ki ki-close"></i>
        </button>
    </div>

    {{-- Modal Body --}}
    <div class="modal-body">
        <div class="col-md-12">
            <div id="trip-sheet">

                {{-- Header Logo & Info --}}
                <div style="width:100%; margin-bottom: 10px;">
                    <div style="width:35%; float:left;">
                        <img src="" alt="Logo" height="60">
                    </div>
                    <div style="width:65%; float:right; text-align:right; font-size:12px;">
                        <p>Printed by: <strong>{{ auth()->user()->name ?? 'Admin' }}</strong></p>
                        <p>Date & Time: <strong>{{ now()->format('d M Y h:i A') }}</strong></p>
                    </div>
                </div>

                {{-- Staff Info Table --}}
                <div class="table-responsive-lg">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td width="15%" style="background:#f6f6f6;" class="text-left">Bus Reg. No :</td>
                                <td width="20%" class="text-left">
                                    <strong>{{ $trip->bus->registration_number ?? '' }}</strong>
                                </td>
                                <td width="10%" style="background:#f6f6f6;" class="text-left">Driver :</td>
                                <td width="20%" class="text-left"><strong></strong></td>
                                <td width="10%" style="background:#f6f6f6;" class="text-left">Supervisor :</td>
                                <td width="25%" class="text-left"><strong></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Trip Summary Table --}}
                <div class="table-responsive-lg">
                    <table class="table table-bordered">
                        <thead>
                            <tr style="background:#f6f6f6;">
                                <th class="text-center">Coach No</th>
                                <th class="text-center">Route Name</th>
                                <th class="text-center">Journey Date</th>
                                <th class="text-center">Sold</th>
                                <th class="text-center">Booked</th>
                                <th class="text-center">Avail</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">{{ $trip->name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $trip->route->name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    {{ date('d M Y', strtotime($trip->start_time)) }}
                                    <span class="text-red">{{ date('h:i A', strtotime($trip->start_time)) }}</span>
                                </td>
                                <td class="text-center">{{ $soldCount ?? 0 }}</td>
                                <td class="text-center">{{ $bookedCount ?? 0 }}</td>
                                <td class="text-center">{{ $availableSeats ?? 0 }}</td>
                                <td class="text-center">{{ $totalSeats ?? 0 }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Passenger List Table --}}

                <div class="table-responsive-lg">
                    <table class="table table-bordered" style="font-size:11px;">
                        <thead>
                            <tr style="background:#efefef;">
                                <th class="text-center">Seats</th>
                                <th class="text-left">Passenger</th>
                                <th class="text-center">Mobile</th>
                                <th class="text-center">Ticket No</th>
                                <th class="text-center">Boarding</th>
                                <th class="text-center">Dropping</th>
                                <th class="text-center">Issued By</th>
                                <th class="text-center">Issue Counter</th>
                                <th class="text-center">Comm.</th>
                                <th class="text-center">Disc.</th>
                                <th class="text-center">Fare</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalFare = 0; @endphp
                            @foreach($tickets as $ticket)
                                @php
                                    $seatNumbers = $ticket->seat_numbers ?? 'N/A';
                                    $boarding = $ticket->boardingCounter->name ?? $ticket->fromStation->name ?? 'N/A';
                                    $dropping = $ticket->droppingCounter->name ?? $ticket->toStation->name ?? 'N/A';
                                    $issuedBy = $ticket->issuedBy->name ?? 'N/A';
                                    $issueCounter = $ticket->boardingCounter->name ?? 'Online';
                                    $comm = $ticket->counter_commission_amount ?? 0;
                                    $disc = $ticket->discount_amount ?? 0;
                                    $fare = $ticket->grand_total ?? 0;

                                    // Only add fare for "sold" and "booked" tickets
                                    if ($ticket->status_label === 'Sold') {
                                        $totalFare += $fare;
                                    } else {
                                        $fare = 0; // Show 0 fare for "Booked" tickets
                                    }
                                @endphp
                                <tr>
                                    <!-- "Cancelled" টিকেটের জন্য সেলগুলো সম্পূর্ণরূপে বাদ দেওয়া হবে -->
                                    <td class="text-center"
                                        style="{{ $ticket->status_label === 'Cancelled' ? 'display:none;' : '' }}">
                                        {{ $seatNumbers }}
                                    </td>
                                    <td style="{{ $ticket->status_label === 'Cancelled' ? 'display:none;' : '' }}">
                                        {{ $ticket->customer_name ?? 'N/A' }}
                                    </td>
                                    <td class="text-center"
                                        style="{{ $ticket->status_label === 'Cancelled' ? 'display:none;' : '' }}">
                                        {{ $ticket->customer_mobile ?? '' }}
                                    </td>
                                    <td class="text-center"
                                        style="{{ $ticket->status_label === 'Cancelled' ? 'display:none;' : '' }}">
                                        {{ ltrim($ticket->pnr_no ?? '', 'INV-') }}
                                    </td>
                                    <td class="text-center"
                                        style="{{ $ticket->status_label === 'Cancelled' ? 'display:none;' : '' }}">
                                        {{ $boarding }}
                                    </td>
                                    <td class="text-center"
                                        style="{{ $ticket->status_label === 'Cancelled' ? 'display:none;' : '' }}">
                                        {{ $dropping }}
                                    </td>
                                    <td class="text-center"
                                        style="{{ $ticket->status_label === 'Cancelled' ? 'display:none;' : '' }}">
                                        @if($ticket->issued_by === 'web' || $ticket->issued_by === '0' || $ticket->issued_by === 0)
                                            <span class="badge badge-success">Web</span>
                                        @else
                                            {{ $issuedBy }}
                                        @endif
                                    </td>

                                    <td class="text-center"
                                        style="{{ $ticket->status_label === 'Cancelled' ? 'display:none;' : '' }}">
                                        @if($ticket->issue_counter_id === 'Online' || $ticket->issue_counter_id === '0' || $ticket->issue_counter_id === 0)
                                            <span class="badge badge-info">Online</span>
                                        @else
                                            {{ $issueCounter }}
                                        @endif
                                    </td>
                                    <td class="text-center"
                                        style="{{ $ticket->status_label === 'Cancelled' ? 'display:none;' : '' }}">
                                        {{ number_format($comm) }}
                                    </td>
                                    <td class="text-center"
                                        style="{{ $ticket->status_label === 'Cancelled' ? 'display:none;' : '' }}">
                                        {{ number_format($disc) }}
                                    </td>
                                    <td class="text-right"
                                        style="{{ $ticket->status_label === 'Cancelled' ? 'display:none;' : '' }}">
                                        {{ number_format($fare) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:#f6f6f6;font-weight:bold;">
                                <td colspan="10" class="text-right">Grand Total:</td>
                                <td class="text-right">{{ number_format($totalFare) }}</td>
                            </tr>
                        </tfoot>
                    </table>

                </div>

                {{-- Counter Sales Summary --}}
                @php
                    $counterSalesSummary = [];

                    // Grand totals for the footer
                    $grandTotalSeats = 0;
                    $tripGrandTotal = 0;
                    $tripTotalDiscount = 0;
                    $tripTotalCounterComm = 0;
                    $tripTotalCallermanComm = 0;
                    $tripTotalGoods = 0;
                    $tripTotalFare = 0;

                    foreach ($tickets as $ticket) {
                        // Identification Logic for Web/Online Bookings
                        $isWebBooking = ($ticket->issued_by === 'web' || $ticket->issue_counter_id === 'Online' || $ticket->issue_counter_id === '0');

                        // Set Display Names for Web vs Counter
                        $counterName = $isWebBooking ? 'Online' : ($ticket->issuecounter->name ?? 'N/A Counter');
                        $counterMaster = $isWebBooking ? 'Web' : ($ticket->issuedBy->name ?? 'N/A Master');

                        $totalSeats = $ticket->seats_count ?? 1;
                        $unitFare = $ticket->fare ?? 0;
                        $subTotal = $ticket->sub_total ?? ($unitFare * $totalSeats);

                        $discount = $ticket->discount_amount ?? 0;
                        $counterComm = $ticket->counter_commission_amount ?? 0;
                        $callermanComm = $ticket->callerman_commission ?? 0;
                        $goods = $ticket->goods_charge ?? 0;
                        $totalCommission = $counterComm + $callermanComm;

                        // Amount received calculation
                        $receive = $ticket->grand_total ?? ($subTotal + $goods - $discount - $totalCommission);

                        if ($ticket->status_label === 'Sold') {
                            // Include counter name and master name in the unique key
                            $key = $counterName . '|' . $counterMaster;

                            if (!isset($counterSalesSummary[$key])) {
                                $counterSalesSummary[$key] = [
                                    'counter_name' => $counterName,
                                    'counter_master' => $counterMaster,
                                    'unit_fare' => $unitFare,
                                    'sold_count' => 0,
                                    'total_fare' => 0,
                                    'total_discount' => 0,
                                    'total_counter_comm' => 0,
                                    'total_callerman_comm' => 0,
                                    'total_goods' => 0,
                                    'total_master_receive' => 0,
                                    'seat_list' => [],
                                ];
                            }

                            // Update Summary Aggregates
                            $counterSalesSummary[$key]['sold_count'] += $totalSeats;
                            $counterSalesSummary[$key]['total_fare'] += $subTotal;
                            $counterSalesSummary[$key]['total_discount'] += $discount;
                            $counterSalesSummary[$key]['total_counter_comm'] += $counterComm;
                            $counterSalesSummary[$key]['total_callerman_comm'] += $callermanComm;
                            $counterSalesSummary[$key]['total_goods'] += $goods;
                            $counterSalesSummary[$key]['total_master_receive'] += $receive;

                            // Aggregating seats
                            $seats = is_array($ticket->seat_numbers) ? implode(', ', $ticket->seat_numbers) : $ticket->seat_numbers;
                            $counterSalesSummary[$key]['seat_list'][] = $seats;

                            // Update Grand Totals
                            $grandTotalSeats += $totalSeats;
                            $tripTotalFare += $subTotal;
                            $tripTotalDiscount += $discount;
                            $tripTotalCounterComm += $counterComm;
                            $tripTotalCallermanComm += $callermanComm;
                            $tripTotalGoods += $goods;
                            $tripGrandTotal += $receive;
                        }
                    }
                @endphp


                <div class="table-responsive-lg">
                    <table class="table table-bordered" style="width:100%;">
                        <thead>
                            <tr>
                                <th colspan="10" class="text-center">Counter Sales Summary</th>
                            </tr>
                            <tr style="background:#f6f6f6;">
                                <th class="text-center">Counter Name</th>
                                <th class="text-center">Sold</th>
                                <th class="text-center">Counter Master</th>
                                <th class="text-center">Fare</th>
                                <th class="text-center">Discount</th>
                                <th class="text-center">Counter Comm.</th>
                                <th class="text-center">Callerman Comm.</th>
                                <th class="text-center">Goods Charge</th>
                                <th class="text-center" colspan="2">Receive Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($counterSalesSummary as $summary)
                                @php
                                    if ($summary['sold_count'] > 0) {
                                        $finalReceive = $summary['total_master_receive'];
                                        $fareFormatted = number_format($summary['unit_fare'], 0) . ' x ' . $summary['sold_count'] . ' = ' . number_format($summary['total_fare'], 0);

                                        // Update overall trip totals for the footer
                                        $grandTotalSeats += $summary['sold_count'];
                                        $tripGrandTotal += $finalReceive;
                                        $tripTotalDiscount += $summary['total_discount'];
                                        $tripTotalCounterComm += $summary['total_counter_comm'];
                                        $tripTotalCallermanComm += $summary['total_callerman_comm'];
                                        $tripTotalGoods += $summary['total_goods'];
                                        $tripTotalFare += $summary['total_fare'];
                                    } else {
                                        $finalReceive = 0;
                                        $fareFormatted = '0';
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        {{ $summary['counter_name'] }}<br>
                                        <small>({{ implode(', ', $summary['seat_list']) }})</small>
                                    </td>
                                    <td class="text-center">{{ $summary['sold_count'] }}</td>
                                    <td class="text-center">{{ $summary['counter_master'] }}</td>
                                    <td class="text-right">{{ $fareFormatted }}</td>
                                    <td class="text-center">{{ number_format($summary['total_discount'], 0) }}</td>
                                    <td class="text-center">{{ number_format($summary['total_counter_comm'], 0) }}</td>
                                    <td class="text-center">{{ number_format($summary['total_callerman_comm'], 0) }}</td>
                                    <td class="text-center">{{ number_format($summary['total_goods'], 0) }}</td>
                                    <td class="text-right" colspan="2">{{ number_format($finalReceive, 0) }}</td>
                                </tr>
                            @endforeach

                            {{-- 🚨 FIX 2: FINAL TOTAL ROW (Ensuring all columns have data) --}}
                            <tr style="background:#f6f6f6; font-weight:bold;">
                                <td class="text-center">Trip Total</td>
                                <td class="text-center">{{ $grandTotalSeats }}</td>
                                <td class="text-center"></td>
                                <td class="text-right">{{ number_format($tripTotalFare, 0) }}</td>
                                <td class="text-center">{{ number_format($tripTotalDiscount, 0) }}</td>
                                <td class="text-center">{{ number_format($tripTotalCounterComm, 0) }}</td>
                                <td class="text-center">{{ number_format($tripTotalCallermanComm, 0) }}</td>
                                <td class="text-center">{{ number_format($tripTotalGoods, 0) }}</td>
                                <td class="text-right" colspan="2">{{ number_format($tripGrandTotal, 0) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="width:100%; text-align:center; margin-top:20px; font-size:10px;">
                    Powered by <a href="#" target="_blank">Test</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Footer --}}
    <div class="modal-footer no-print">
        <div class="col-md-12 text-right">
            <button class="btn btn-warning" onclick="PrintTripSheetThermal();">THERMAL PRINT</button>
            <button class="btn btn-success" onclick="PrintTripSheet();">PRINT ALL</button>
            <button class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
        </div>
    </div>
</div>