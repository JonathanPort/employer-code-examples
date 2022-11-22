<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\UserOnboarding\FirmAccessRequest;
use App\Mail\UserOnboarding\FirmAccessRequestDeclined as FARD;
use App\Events\UserOnboarding\DeclineFirmAccessRequest;
use App\Services\UserOnboardingService;

class FirmAccessRequestDeclined
{
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
     * @param  \App\Events\DeclineFirmAccessRequest  $event
     * @return void
     */
    public function handle(DeclineFirmAccessRequest $event)
    {

        $model = $event->model;
        $request = $event->request;

        $request->updateStatus(FirmAccessRequest::STATUS__DECLINED);

        $model->updateStatus(UserOnboardingService::STATUS__FIRM_REQUEST_ACCESS_DECLINED);

        Mail::to($model->email)->queue(new FARD($model));

    }
}
