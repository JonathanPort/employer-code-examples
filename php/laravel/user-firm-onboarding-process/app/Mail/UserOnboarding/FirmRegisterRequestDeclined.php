<?php

namespace App\Mail\UserOnboarding;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FirmRegisterRequestDeclined extends Mailable
{
    use Queueable, SerializesModels;

    public $model;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($model)
    {

        $this->model = $model;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Firm register request has been declined.')
                    // ->from('no-reply@EXAMPLEAPPNAME.co.uk')
                    ->from('contact@EXAMPLEAPPNAME.co.uk')
                    ->view('user-onboarding.mail.firm-register-request-declined');
    }

}
