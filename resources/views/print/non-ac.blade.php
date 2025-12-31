<!DOCTYPE html>
<html>
<head>
    <title>Ticket Print - {{ $ticket->pnr_no ?? 'N/A' }}</title>
    <style type="text/css">
        /* --- GENERAL STYLES --- */
        body { 
            font-family: sans-serif; 
            margin: 0; 
            padding: 5px; /* Added slight padding for margins */
            font-size: 10px;
        }
        
        /* --- TRIPLE COLUMN CONTAINER (Main structure) --- */
        .print-container { 
            width: 100%; 
            display: flex; 
            flex-wrap: nowrap;
            justify-content: space-between;
        }
        
        /* --- COLUMN STYLES (Each is 1/3rd of the page) --- */
        .ticket-col {
            flex-basis: 33%; /* Approximately 1/3rd */
            padding: 0 5px;
            box-sizing: border-box;
            border-right: 1px dashed #ddd; 
        }
        .ticket-col:last-child {
            border-right: none;
        }
        
        /* --- TABLE STYLES --- */
        .pc-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Ensures fixed column widths within table */
            margin-top: 5px; 
        }
        .pc-table td {
            font-size: 11px;
            padding: 1px 1px;
            vertical-align: top;
            line-height: 13px;
        }
        
        /* Consistent Label/Value Alignment (Using fixed % within the table structure) */
        .pc-table .label { 
            text-align: right; 
            width: 35%; /* Fixed width for consistent alignment */
            font-weight: normal;
        }
        .pc-table .value { 
            text-align: left; 
            width: 65%;
            font-weight: bold;
        }
        .pc-table .name-value { font-size: 13px; font-weight: bold; }
        .pc-table .split-label { width: 18%; text-align: right; }
        .pc-table .split-value { width: 32%; text-align: left; font-weight: bold; }

        /* Print Media Query */
        @media print {
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="print-container">
        
        {{-- Loop for THREE copies: $i=0 (Main), $i=1 (Counterfoil 1), $i=2 (Counterfoil 2) --}}
        @for ($i = 0; $i < 3; $i++)
            <div class="ticket-col">
                <table class="pc-table">
                    <tbody>
                        
                        {{-- Coach --}}
                        <tr><td class="label">Coach:</td><td class="value large-value" colspan="3">{{ $ticket->schedule?->name ?? 'N/A' }}</td></tr>
                        
                        {{-- Passenger --}}
                        <tr><td class="label">Passenger:</td><td class="value name-value" colspan="3">{{ $ticket->customer_name ?? 'N/A' }}</td></tr>
                        
                        {{-- Mobile & PNR/Mobile & PNR (Side-by-side in your original structure) --}}
                        <tr>
                            <td class="label">Mobile:</td><td class="value" colspan="3">{{ $ticket->customer_mobile ?? 'N/A' }}</td>
                        </tr>
                         {{-- PNR (Moved to single row for alignment safety) --}}
                        <tr>
                            <td class="label">PNR:</td><td class="value" colspan="3">{{ $ticket->pnr_no ?? 'N/A' }}</td>
                        </tr>
                        
                        {{-- Seats --}}
                        <tr><td class="label">Seats:</td><td class="value name-value" colspan="3">[ {{ $ticket->seat_numbers ?? 'N/A' }} ]</td></tr>
                        
                        {{-- Financials --}}
                        <tr><td class="label">Seat Fare:</td><td class="value" colspan="3">{{ number_format($ticket->fare ?? 0) }} TK</td></tr>
                        <tr>
                            <td class="label">Total Fare:</td>
                            <td class="value" colspan="3">
                                {{ ($ticket->seats_count ?? 1) }} * {{ number_format($ticket->fare ?? 0) }} = {{ number_format($ticket->grand_total ?? 0) }} TK
                            </td>
                        </tr>
                        
                        {{-- Journey Date --}}
                        <tr><td class="label">Journey Date:</td><td class="value" colspan="3">{{ date('j M Y', strtotime($ticket->journey_date ?? now())) }}</td></tr>

                        {{-- Route & Boarding/Dropping --}}
                        <tr><td class="label">From:</td><td class="value" colspan="3">{{ $ticket->fromStation?->name ?? 'N/A' }}</td></tr>
                        <tr><td class="label">To:</td><td class="value" colspan="3">{{ $ticket->toStation?->name ?? 'N/A' }}</td></tr>
                        <tr><td class="label">Boarding:</td><td class="value" colspan="3">{{ $ticket->boardingCounter?->name ?? $ticket->fromStation?->name ?? 'N/A' }}</td></tr>
                        <tr><td class="label">Dropping:</td><td class="value" colspan="3">{{ $ticket->droppingCounter?->name ?? $ticket->toStation?->name ?? 'N/A' }}</td></tr>

                        {{-- Departure & Reporting --}}
                        <tr>
                            <td class="label">Departure:</td><td class="value" colspan="3">{{ date('h:i A', strtotime($ticket->schedule?->start_time ?? now())) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Reporting:</td><td class="value" colspan="3">{{ date('h:i A', strtotime($ticket->schedule?->start_time ?? now()) - 20 * 60) }}</td>
                        </tr>

                        {{-- Issued Info --}}
                        <tr><td class="label">Issued At:</td><td class="value" colspan="3">{{ $ticket->created_at?->format('d-m-Y h:i A') ?? 'N/A' }}</td></tr>
                        <tr><td class="label">Issued By:</td><td class="value" colspan="3">{{ $ticket->issuedBy?->name ?? 'N/A' }}, {{ $ticket->issueCounter?->name ?? 'N/A' }}</td></tr>
                        
                        {{-- Printed By --}}
                        <tr>
                            <td class="label">Printed By:</td><td class="value" colspan="3">{{ Auth::user()->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                        <td class="label">Printed At:</td>
                        {{-- সার্ভারের বর্তমান সময় প্রিন্ট করা হচ্ছে --}}
                        <td class="value" colspan="3">{{ now()->format('d-m-Y h:i A') }}</td> 
                    </tr>
                        {{-- Footer Links (Only on the first copy) --}}
                        @if ($i === 0)
                            <tr><td class="text-center" colspan="4" style="padding-top: 5px; font-weight: normal;">uniqueservice.xyz</td></tr>
                            <tr><td class="text-center" colspan="4" style="font-weight: normal;">www.chokrojan.com</td></tr>
                        @endif
                        
                    </tbody>
                </table>
            </div>
        @endfor
    </div>
</body>
</html>