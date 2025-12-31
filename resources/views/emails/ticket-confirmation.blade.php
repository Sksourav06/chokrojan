<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Confirmation</title>
    <style>
        /* Basic inline styles for email client compatibility */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #007bff;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px 30px;
        }

        .footer {
            background-color: #f0f0f0;
            color: #666;
            padding: 15px 30px;
            font-size: 12px;
            text-align: center;
            border-top: 1px solid #ddd;
        }

        h2 {
            margin-top: 0;
            color: #007bff;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .detail-table th,
        .detail-table td {
            padding: 8px 0;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .detail-table th {
            width: 40%;
            font-weight: bold;
            color: #555;
        }

        .total {
            font-size: 16px;
            font-weight: bold;
            color: #d9534f;
        }

        .note {
            margin-top: 20px;
            padding: 10px;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            border-radius: 4px;
            font-size: 13px;
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="header">
            <h1>Ticket Confirmed!</h1>
            <p>Your journey details are ready. Please review the information below.</p>
        </div>

        <div class="content">
            <h2>{{ $ticket->customer_name ?? 'Valued Customer' }}</h2>
            <p>Thank you for booking with us. Here is your ticket summary:</p>

            <table class="detail-table">
                <tbody>

                    <tr>
                        <th>PNR Number:</th>
                        <td><strong>{{ $ticket->pnr_no ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <th>Mobile:</th>
                        <td>{{ $ticket->customer_mobile ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $ticket->passenger_email ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>

            <h3 style="color: #007bff; border-bottom: 2px solid #eee; padding-bottom: 5px;">Journey Details</h3>
            <table class="detail-table">
                <tbody>
                    <tr>
                        <th>Route:</th>
                        <td>{{ $ticket->fromStation?->name ?? 'N/A' }} to {{ $ticket->toStation?->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Seats Booked:</th>
                        <td><strong>{{ $ticket->seat_numbers ?? 'N/A' }} ({{ $ticket->seats_count ?? 0 }}
                                seats)</strong></td>
                    </tr>
                    <tr>
                        <th>Departure Date:</th>
                        <td>{{ date('j M Y', strtotime($ticket->journey_date ?? now())) }}</td>
                    </tr>
                    <tr>
                        <th>Departure Time:</th>
                        <td>{{ date('h:i A', strtotime($ticket->schedule?->start_time ?? now())) }}</td>
                    </tr>
                    <tr>
                        <th>Bus/Coach:</th>
                        <td>{{ $ticket->schedule?->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Boarding Point:</th>
                        <td>{{ $ticket->boardingCounter?->name ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>

            <h3 style="color: #007bff; border-bottom: 2px solid #eee; padding-bottom: 5px;">Fare Summary</h3>
            <table class="detail-table">
                <tbody>
                    <tr>
                        <th>Sub Total:</th>
                        <td>{{ number_format($ticket->sub_total ?? 0, 2) }} Tk</td>
                    </tr>
                    <tr>
                        <th>Discount:</th>
                        <td>- {{ number_format($ticket->discount_amount ?? 0, 2) }} Tk</td>
                    </tr>
                    <tr>
                        <th>Service Charge:</th>
                        <td>+ {{ number_format($ticket->service_charge ?? 0, 2) }} Tk</td>
                    </tr>
                    <tr class="total">
                        <th>GRAND TOTAL:</th>
                        <td>{{ number_format($ticket->grand_total ?? 0, 2) }} Tk</td>
                    </tr>
                </tbody>
            </table>

            <div class="note">
                Please arrive at the boarding counter at least 20 minutes before departure time. This email serves as
                your e-ticket.
            </div>

        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>

    </div>

</body>

</html>