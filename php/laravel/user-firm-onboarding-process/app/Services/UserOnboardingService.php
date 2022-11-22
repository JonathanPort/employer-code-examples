<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Users\User;
use App\Models\UserOnboarding\UserOnboardingProcessData;
use App\Models\UserOnboarding\UserOnboardingProcess;
use App\Models\UserOnboarding\FirmRegisterRequest;
use App\Models\UserOnboarding\FirmAccessRequest;
use App\Events\UserOnboarding\SubmitFirmRegisterRequest;
use App\Events\UserOnboarding\SubmitFirmAccessRequest;

class UserOnboardingService
{

    private $process;

    private const SESSION_KEY = 'user_onboarding_process_id';

    // Statuses
    public const STATUS__STARTED = 'onboarding_started';
    public const STATUS__PERSONAL_DATA_SUBMITTED = 'personal_data_submitted';
    public const STATUS__FIRM_REQUEST_ACCESS_SUBMITTED = 'firm_access_request_submitted';
    public const STATUS__FIRM_REQUEST_ACCESS_APPROVED = 'firm_access_request_approved';
    public const STATUS__FIRM_REQUEST_ACCESS_DECLINED = 'firm_access_request_declined';
    public const STATUS__FIRM_REGISTER_SUBMITTED = 'firm_register_submitted';
    public const STATUS__FIRM_REGISTER_APPROVED = 'firm_register_approved';
    public const STATUS__FIRM_REGISTER_DECLINED = 'firm_register_declined';
    public const STATUS__PASSWORD_SET = 'password_set';
    public const STATUS__PROCESS_COMPLETE = 'process_complete';

    // Routes
    public const ROUTE__START = 'user-onboarding.start';
    public const ROUTE__PERSONAL_DATA = 'user-onboarding.personal-data';
    public const ROUTE__FIRM_ACCESS = 'user-onboarding.firm-access';
    public const ROUTE__FIRM_REQUEST_ACCESS = 'user-onboarding.firm-request-access';
    public const ROUTE__FIRM_REQUEST_PENDING = 'user-onboarding.firm-request-pending';
    public const ROUTE__FIRM_REGISTER = 'user-onboarding.firm-register';
    public const ROUTE__FIRM_REGISTER_PENDING = 'user-onboarding.firm-register-pending';
    public const ROUTE__SET_PASSWORD = 'user-onboarding.set-password';
    public const ROUTE__GET_STARTED = 'user-onboarding.get-started';
    public const ROUTE__DASHBOARD = 'dashboard';


    private function getProcessIdFromSession()
    {

        return session()->get(self::SESSION_KEY);

    }


    private function putProcessIdIntoSession(string $id)
    {

        return session()->put(self::SESSION_KEY, $id);

    }


    private function getProcessRecord(string $processId)
    {

        if (class_basename($this->process) === UserOnboardingProcess::class) {

            return $this->user;

        } else {

            return UserOnboardingProcess::find($processId);

        }

    }


    private function createProcessRecord()
    {

        // Maybe catch UTM Params, IP address and other usefull info

        return UserOnboardingProcess::create([
            //
        ]);

    }


    private function updateProcessRecord(array $data)
    {

        $process = $this->getProcess();

        if (! $process) throw new \Exception('No Process found in session.');

        return $process->update($data);

    }


    private function updateProcessData(array $data)
    {

        $process = $this->getProcess();
        $existingKeys = array_keys($data);
        $existing = UserOnboardingProcessData::whereIn('key', $existingKeys)->get();

        foreach ($data as $key => $value) UserOnboardingProcessData::create([
            'user_onboarding_process_id' => $process->id,
            'key' => $key,
            'value' => $value,
        ]);

        foreach ($existing as $record) $record->delete();

        $this->updateProcessRecord([
            'updated_at' => Carbon::now(),
        ]);

        return true;

    }


    public function getNextProcessRoute()
    {

        if (! $this->checkOnboardingProcessExists()) {
            return [
                'route' => self::ROUTE__START,
            ];
        }

        $process = $this->getProcess();

        $status = $process->status;

        switch ($status) {

            case self::STATUS__STARTED:
                return [
                    'route' => self::ROUTE__PERSONAL_DATA,
                ];
            case self::STATUS__PERSONAL_DATA_SUBMITTED:
                return [
                    'route' => self::ROUTE__FIRM_ACCESS,
                ];
            case self::STATUS__FIRM_REQUEST_ACCESS_SUBMITTED:
                return [
                    'route' => self::ROUTE__FIRM_REQUEST_PENDING,
                    'flash' => [
                        'type' => 'success',
                        'msg' => 'Firm access pending.',
                    ],
                ];
            case self::STATUS__FIRM_REQUEST_ACCESS_APPROVED:
                return [
                    'route' => self::ROUTE__SET_PASSWORD,
                    'flash' => [
                        'type' => 'success',
                        'msg' => 'You have been approved and your account has been created successfully.',
                    ],
                ];
            case self::STATUS__FIRM_REQUEST_ACCESS_DECLINED:
                return [
                    'route' => self::ROUTE__FIRM_ACCESS,
                    'flash' => [
                        'type' => 'error',
                        'msg' => 'Your request to access firm has been declined.',
                    ],
                ];
            case self::STATUS__FIRM_REGISTER_SUBMITTED:
                return [
                    'route' => self::ROUTE__FIRM_REGISTER_PENDING,
                    'flash' => [
                        'type' => 'success',
                        'msg' => 'Firm Register Pending',
                    ],
                ];
            case self::STATUS__FIRM_REGISTER_APPROVED:
                return [
                    'route' => self::ROUTE__SET_PASSWORD,
                    'flash' => [
                        'type' => 'success',
                        'msg' => 'You have been approved and your account has been created successfully.',
                    ],
                ];
            case self::STATUS__FIRM_REGISTER_DECLINED:
                return [
                    'route' => self::ROUTE__FIRM_ACCESS,
                    'flash' => [
                        'type' => 'error',
                        'msg' => 'Your request to access firm has been declined.',
                    ],
                ];
            case self::STATUS__PASSWORD_SET:
                return [
                    'route' => self::ROUTE__GET_STARTED,
                ];
            case self::STATUS__PROCESS_COMPLETE:
                return [
                    'route' => self::ROUTE__DASHBOARD,
                    'flash' => [
                        'type' => 'success',
                        'msg' => 'Account created successfully.',
                    ],
                ];

        }

    }


    public function startNewOnboardingProcess()
    {

        $record = $this->createProcessRecord();

        $this->putProcessIdIntoSession($record->id);

        $record->updateStatus(self::STATUS__STARTED);

        return true;

    }


    public function continueProcess(string $processId)
    {

        return $this->putProcessIdIntoSession($processId);

    }


    public function checkOnboardingProcessExists()
    {

        return $this->getProcess() ? true : false;

    }


    public function getProcess()
    {

        $id = $this->getProcessIdFromSession();

        return $id ? $this->getProcessRecord($id) : false;

    }


    public function getProcessData()
    {

        $process = $this->getProcess();

        if (! $process) throw new \Exception('No process found in session.');

        $data = $process->data()->get();
        $arr = [];

        foreach ($data as $row) $arr[$row->key] = $row->value;

        return $arr;

    }


    public function validatePersonalData(array $data)
    {

        return Validator::make($data, [
            'pd_title' => 'required|string',
            'pd_first_name' => 'required|string',
            'pd_last_name' => 'required|string',
            'pd_gender' => 'required|string',
            'pd_role' => 'required|string|in:solicitor,mediator',
            'email' => 'required|email|unique:users',
        ], []);

    }


    public function verifyPersonalDataIsCaptured()
    {

        if (! $this->checkOnboardingProcessExists()) return false;

        $data = $this->getProcessData();

        $validator = $this->validatePersonalData($data);

        return $validator->fails() ? false : true;

    }


    public function submitPersonalData(array $data)
    {

        $process = $this->getProcess();

        $this->updateProcessData($data);

        $process->updateStatus(self::STATUS__PERSONAL_DATA_SUBMITTED);

        return true;

    }


    public function validateFirmAccessRequestData(array $data)
    {

        return Validator::make($data, [
            'fra_company_name' => 'required|string',
            'fra_company_number' => 'required|string',
        ], []);

    }


    public function verifyFirmAccessRequestDataIsCaptured()
    {

        if (! $this->checkOnboardingProcessExists()) return false;

        $data = $this->getProcessData();

        $validator = $this->validateFirmAccessRequestData($data);

        return $validator->fails() ? false : true;

    }


    public function submitFirmAccessRequest(array $data)
    {

        $process = $this->getProcess();

        $this->updateProcessData($data);

        $process->updateStatus(self::STATUS__FIRM_REQUEST_ACCESS_SUBMITTED);

        $data = $this->getProcessData();

        $data['process_id'] = $process->id;

        SubmitFirmAccessRequest::dispatch($process);

        return true;

    }


    public function checkFirmAccessRequestIsApproved()
    {

        if (! $this->checkOnboardingProcessExists()) return false;

        $process = $this->getProcess();

        $request = FirmAccessRequest::where('requester_model_name', get_class($process))
                                    ->where('requester_model_id', $process->id)
                                    ->latest()
                                    ->first();

        if ($request->status === FirmAccessRequest::STATUS__APPROVED) {
            return true;
        } else {
            return false;
        }

    }


    public function checkFirmAccessRequestIsDeclined()
    {

        if (! $this->checkOnboardingProcessExists()) return false;

        $process = $this->getProcess();

        $request = FirmAccessRequest::where('requester_model_name', get_class($process))
                                    ->where('requester_model_id', $process->id)
                                    ->latest()
                                    ->first();

        if ($request->status === FirmAccessRequest::STATUS__DECLINED) {
            return true;
        } else {
            return false;
        }

    }


    public function checkFirmAccessRequestIsPending()
    {

        if (! $this->checkOnboardingProcessExists()) return false;

        $process = $this->getProcess();

        $request = FirmAccessRequest::where('requester_model_name', get_class($process))
                                    ->where('requester_model_id', $process->id)
                                    ->latest()
                                    ->first();

        if ($request->status === FirmAccessRequest::STATUS__PENDING) {
            return true;
        } else {
            return false;
        }

    }


    public function checkFirmAccessRequestExists()
    {

        if (! $this->checkOnboardingProcessExists()) return false;

        $process = $this->getProcess();

        $request = FirmAccessRequest::where('requester_model_name', get_class($process))
                                    ->where('requester_model_id', $process->id)
                                    ->first();

        return $request ? true : false;

    }


    public function validateFirmRegisterRequestData(array $data)
    {
        return Validator::make($data, [
            'fr_company_name' => 'required|string',
            'fr_company_number' => 'required|string',
            'company_status' => 'required|string',
            'company_email' => 'required|email',
            'company_tel' => 'required|tel:gb',
            'company_address' => 'required|json',
            'company_number' => 'required|string',
            'vat_tax_id' => 'required|string',
        ], [
            // Custom messages here
        ]);
    }


    public function verifyFirmRegisterDataIsCaptured()
    {

        if (! $this->checkOnboardingProcessExists()) return false;

        $data = $this->getProcessData();

        $validator = $this->validateFirmRegisterRequestData($data);

        return $validator->fails() ? false : true;

    }


    public function submitFirmRegisterRequest(array $data)
    {

        $process = $this->getProcess();

        $this->updateProcessData($data);

        $process->updateStatus(self::STATUS__FIRM_REGISTER_SUBMITTED);

        $data = $this->getProcessData();

        $data['process_id'] = $process->id;

        SubmitFirmRegisterRequest::dispatch($process);

        return true;

    }


    public function checkFirmRegisterRequestIsApproved()
    {

        if (! $this->checkOnboardingProcessExists()) return false;

        $process = $this->getProcess();

        $request = FirmRegisterRequest::where('requester_model_name', get_class($process))
                                    ->where('requester_model_id', $process->id)
                                    ->latest()
                                    ->first();

        if ($request->status === FirmRegisterRequest::STATUS__APPROVED) {
            return true;
        } else {
            return false;
        }

    }


    public function checkFirmRegisterRequestIsDeclined()
    {

        if (! $this->checkOnboardingProcessExists()) return false;

        $process = $this->getProcess();

        $request = FirmRegisterRequest::where('requester_model_name', get_class($process))
                                    ->where('requester_model_id', $process->id)
                                    ->latest()
                                    ->first();

        if ($request->status === FirmRegisterRequest::STATUS__DECLINED) {
            return true;
        } else {
            return false;
        }

    }


    public function checkFirmRegisterRequestIsPending()
    {

        if (! $this->checkOnboardingProcessExists()) return false;

        $process = $this->getProcess();

        $request = FirmRegisterRequest::where('requester_model_name', get_class($process))
                                    ->where('requester_model_id', $process->id)
                                    ->latest()
                                    ->first();

        if ($request->status === FirmRegisterRequest::STATUS__PENDING) {
            return true;
        } else {
            return false;
        }

    }


    public function checkFirmRegisterRequestExists()
    {

        if (! $this->checkOnboardingProcessExists()) return false;

        $process = $this->getProcess();

        $request = FirmRegisterRequest::where('requester_model_name', get_class($process))
                                    ->where('requester_model_id', $process->id)
                                    ->first();

        return $request ? true : false;

    }


    public function verifyUserAccountIsCreated()
    {

        if (! $this->checkOnboardingProcessExists()) return false;

        $process = $this->getProcess();

        if (! $process->completed_user_id) return false;

        $user = User::find($process->completed_user_id);

        return $user ? $user : false;

    }


    public function validatePassword(string $password)
    {

        return Validator::make(['password' => $password], [
            'password' => 'required|min:8',
        ], []);

    }


    public function submitPassword(string $password)
    {

        if (! $this->checkOnboardingProcessExists()) return false;

        $user = $this->verifyUserAccountIsCreated();

        if (! $user) throw new \Exception('User account has not been created.');

        $password = Hash::make($password);

        $user->update([
            'password' => $password,
        ]);

        $process = $this->getProcess();

        $process->updateStatus(self::STATUS__PASSWORD_SET);

        return true;

    }


    public function completeProcess()
    {

        if (! $this->checkOnboardingProcessExists()) return false;

        $user = $this->verifyUserAccountIsCreated();

        if (! $user) throw new \Exception('User account has not been created.');

        $this->updateProcessRecord([
            'completed_at' => Carbon::now(),
        ]);

        $process = $this->getProcess();

        $process->updateStatus(self::STATUS__PROCESS_COMPLETE);

        $data = $process->data()->get();

        foreach ($data as $row) $row->delete();

        Auth::loginUsingId($user->id);

        return true;

    }


}
