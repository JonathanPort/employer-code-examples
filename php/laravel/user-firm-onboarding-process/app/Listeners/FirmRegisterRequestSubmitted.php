<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\UserOnboarding\FirmRegisterRequest;
use App\Mail\UserOnboarding\FirmRegisterRequestedConfirmation;
use App\Events\UserOnboarding\SubmitFirmRegisterRequest;

class FirmRegisterRequestSubmitted implements ShouldQueue
{

    /**
     * The time (seconds) before the job should be processed.
     *
     * @var int
     */
    // public $delay = 60;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SubmitFirmRegisterRequest  $event
     * @return void
     */
    public function handle(SubmitFirmRegisterRequest $event)
    {

        $model = $event->model;

        $request = FirmRegisterRequest::create([
            'requester_model_name' => get_class($model),
            'requester_model_id' => $model->id,
        ]);

        Mail::to($model->email)->queue(new FirmRegisterRequestedConfirmation($model));

    }
}
