<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $code;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        $displayName = $this->user->name ?? $this->user->email;
        return $this->subject(__('emails.verification.subject'))
            ->view('emails.verification-code')
            ->with([
                'user' => $this->user,
                'code' => $this->code,
                'displayName' => $displayName,
            ]);
    }
}