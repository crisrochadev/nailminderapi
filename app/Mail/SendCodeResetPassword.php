<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCodeResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $code;
    public $type;
    public $url;

    public function __construct(Array $data)
    {
        $this->code = $data['code'];
        $this->type = $data['type'];
        $this->url = env('FRONT_URL')."/auth/confirmar-email?token=".$data['code'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->type === 'verify') return $this->markdown('emails.send-email-confirmation')->with('url', $this->url);
        return $this->markdown('emails.send-code-reset-password');
    }
}