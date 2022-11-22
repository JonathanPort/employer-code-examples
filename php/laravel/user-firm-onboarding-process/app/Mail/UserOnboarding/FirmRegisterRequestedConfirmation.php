<?php

namespace App\Mail\UserOnboarding;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FirmRegisterRequestedConfirmation extends Mailable
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
        return $this->subject('Firm register request pending')
                    // ->from('no-reply@EXAMPLEAPPNAME.co.uk')
                    ->from('contact@EXAMPLEAPPNAME.co.uk')
                    ->view('user-onboarding.mail.firm-register-requested-confirmation');
    }
}
