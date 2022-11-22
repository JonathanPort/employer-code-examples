<?php

namespace App\Listeners;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\UserManagementService;
use App\Models\UserOnboarding\UserOnboardingProcess;
use App\Models\UserOnboarding\FirmAccessRequest;
use App\Models\Firms\MediatorFirm;
use App\Mail\UserOnboarding\FirmAccessRequestApproved as FARA;
use App\Events\UserOnboarding\DeclineFirmAccessRequest;
use App\Events\UserOnboarding\ApproveFirmAccessRequest;
use App\Services\UserOnboardingService;

class FirmAccessRequestApproved
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
     * @param  \App\Events\ApproveFirmAccessRequest  $event
     * @return void
     */
    public function handle(ApproveFirmAccessRequest $event)
    {

        $model = $event->model;
        $request = $event->request;

        $request->updateStatus(FirmAccessRequest::STATUS__APPROVED);

        $userManagementService = new UserManagementService();

        $user = $userManagementService->createUser((object)[
            'firm_id' => $request->firm_id,
            'title' => $model->title,
            'first_name' => $model->first_name,
            'last_name' => $model->last_name,
            'email' => $model->email,
            'job_title' => $model->role,
            'gender' => $model->gender,
            'salutation' => 'Test Salutation',
            'password' => Str::random(16),
        ], $model->role);

        $model->update([
            'completed_user_id' => $user->id,
        ]);

        $model->updateStatus(UserOnboardingService::STATUS__FIRM_REQUEST_ACCESS_APPROVED);

        Mail::to($model->email)->queue(new FARA($model));

    }
}
