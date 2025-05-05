<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserCredentials extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * The plaintext password.
     *
     * @var string
     */
    public $password;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\User $user
     * @param string $password
     * @return void
     */
    public function __construct(User $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your New Account Credentials')
                    ->markdown('emails.new-user-credentials');
    }
} 