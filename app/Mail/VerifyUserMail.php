<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class VerifyUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Verifica tu cuenta')
                    ->markdown('emails.verify_user')
                    ->with([
                        'url' => route('auth.verify', $this->user->verification_token),
                        'expires_at' => $this->user->verification_expires_at,
                        'user' => $this->user,
                    ]);
    }
}
