<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function printNonAC(Request $request)
    {
        // 1. Get the data sent from the frontend (ticket_id, data array)
        $ticketId = $request->input('ticket_id');
        $ticketData = $request->input('data');

        // 2. Fetch the full ticket data from the database again for security/completeness
        $ticket = \App\Models\TicketIssue::find($ticketId);

        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        // 3. Render the dedicated print view (e.g., resources/views/print/non-ac.blade.php)
        $printHtml = view('print.non-ac', compact('ticket', 'ticketData'))->render();

        // 4. Return the HTML content to the AJAX success handler
        return response()->json([
            'html' => $printHtml,
        ]);
    }
}
