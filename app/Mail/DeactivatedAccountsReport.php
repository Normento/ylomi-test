<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeactivatedAccountsReport extends Mailable
{
    use Queueable, SerializesModels;

    public $deactivatedAccounts;

    /**
     * Create a new message instance.
     */
    public function __construct($deactivatedAccounts)
    {
        $this->deactivatedAccounts = $deactivatedAccounts;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Deactivated Accounts Report',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        return $this->view('emails.deactivated_accounts_report')
            ->subject('Rapport des comptes désactivés du mois')
            ->with('deactivatedAccounts', $this->deactivatedAccounts);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
