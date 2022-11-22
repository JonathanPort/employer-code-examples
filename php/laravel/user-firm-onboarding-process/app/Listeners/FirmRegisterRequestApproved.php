<?php

namespace App\Listeners;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\UserOnboardingService;
use App\Services\UserManagementService;
use App\Models\UserOnboarding\FirmRegisterRequest;
use App\Mail\UserOnboarding\FirmRegisterRequestApproved as FRRA;
use App\Events\UserOnboarding\ApproveFirmRegisterRequest;
use App\Services\FirmManagementService;

class FirmRegisterRequestApproved
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
     * @param  \App\Events\ApproveFirmRegisterRequest  $event
     * @return void
     */
    public function handle(ApproveFirmRegisterRequest $event)
    {

        $model = $event->model;
        $request = $event->request;

        $request->updateStatus(FirmRegisterRequest::STATUS__APPROVED);


        $firmMananementService = new FirmManagementService();

        $firm = $firmMananementService->createFirm([
            'name' => $model->company_name,
            'company_number' => $model->company_number,
            'type' => $model->role,
            'company_status' => 'LTD',
            'company_email' => 'test@test.com',
            'company_tel' => '07765383933',
            'company_address' => '1234 Far Far Away',
            'vat_tax_id' => '234',
        ]);

        $userManagementService = new UserManagementService();

        $user = $userManagementService->createUser((object)[
            'firm_id' => $firm->id,
            'title' => $model->title,
            'first_name' => $model->first_name,
            'last_name' => $model->last_name,
            'email' => $model->email,
            'job_title' => $model->role,
            'gender' => $model->gender,
            'salutation' => 'Test Salutation',
            'password' => Str::random(16),
        ], $model->role . ' admin');

        $model->update([
            'completed_user_id' => $user->id,
        ]);

        $model->updateStatus(UserOnboardingService::STATUS__FIRM_REGISTER_APPROVED);

        Mail::to($model->email)->queue(new FRRA($model));

    }
}
