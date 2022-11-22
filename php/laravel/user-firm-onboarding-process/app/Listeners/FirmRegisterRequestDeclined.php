<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\UserOnboardingService;
use App\Models\UserOnboarding\FirmRegisterRequest;
use App\Models\UserOnboarding\FirmAccessRequest;
use App\Mail\UserOnboarding\FirmRegisterRequestDeclined as FRRD;
use App\Events\UserOnboarding\DeclineFirmRegisterRequest;
use App\Events\UserOnboarding\DeclineFirmAccessRequest;

class FirmRegisterRequestDeclined
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
     * @param  \App\Events\DeclineFirmRegisterRequest  $event
     * @return void
     */
    public function handle(DeclineFirmRegisterRequest $event)
    {

        $model = $event->model;
        $request = $event->request;

        $request->updateStatus(FirmRegisterRequest::STATUS__DECLINED);

        $model->updateStatus(UserOnboardingService::STATUS__FIRM_REGISTER_DECLINED);

        Mail::to($model->email)->queue(new FRRD($model));

    }
}
