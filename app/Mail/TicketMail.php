<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\TicketIssue; // Import your TicketIssue model

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The TicketIssue instance.
     * @var TicketIssue
     */
    public $ticket;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(TicketIssue $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        // Define the subject of the email, making it dynamic
        $pnr = $this->ticket->pnr_no ?? 'N/A';


        return new Envelope(
            subject: "Ticket Confirmation: PNR {$pnr}",
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        // Define the Blade view that holds the email's HTML content.
        // You MUST create this file at 'resources/views/emails/ticket-confirmation.blade.php'
        return new Content(
            view: 'emails.ticket-confirmation',
            with: [
                // Optionally pass extra data to the view, though $this->ticket is already available.
                'seat_info' => $this->ticket->seat_numbers,
                'total_fare' => $this->ticket->grand_total,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}