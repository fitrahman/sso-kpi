<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Laravel\Passport\Client;

class AppAccessApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $client;

    public function __construct(User $user, Client $client)
    {
        $this->user = $user;
        $this->client = $client;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Akses Aplikasi Disetujui - ' . $this->client->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.app-access-approved',
        );
    }
}
