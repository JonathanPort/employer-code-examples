<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\UserOnboarding\UserOnboardingProcess;
use App\Models\UserOnboarding\FirmAccessRequest;
use App\Models\Firms\Firm;
use App\Mail\UserOnboarding\FirmAccessRequestedConfirmation;
use App\Events\UserOnboarding\SubmitFirmAccessRequest;
use App\Events\UserOnboarding\DeclineFirmAccessRequest;
use App\Mail\UserOnboarding\FirmAccessRequestDeclined;

class FirmAccessRequestSubmitted implements ShouldQueue
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
     * @param  \App\Events\SubmitFirmAccessRequest  $event
     * @return void
     */
    public function handle(SubmitFirmAccessRequest $event)
    {

        $model = $event->model;

        // Check if firm exists
        $firm = Firm::where('company_number', $model->company_number)
                    ->where('type', $model->role)
                    ->first();


        // Handle other verification here

        if ($firm) {

            $request = FirmAccessRequest::create([
                'firm_id' => $firm->id,
                'requester_model_name' => get_class($model),
                'requester_model_id' => $model->id,
            ]);

            Mail::to($model->email)->queue(new FirmAccessRequestedConfirmation($model));

            // TODO: Notify Firm Admin

        } else {

            $request = FirmAccessRequest::create([
                'firm_id' => null,
                'requester_model_name' => get_class($model),
                'requester_model_id' => $model->id,
            ]);

            DeclineFirmAccessRequest::dispatch($request);

        }

    }
}
