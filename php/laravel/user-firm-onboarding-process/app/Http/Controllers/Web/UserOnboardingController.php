<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Services\UserOnboardingService;
use App\Http\Controllers\Controller;
use App\Forms\UserOnboarding\PersonalDataForm;
use App\Forms\UserOnboarding\FirmRequestAccessForm;
use App\Forms\UserOnboarding\FirmRegisterForm;

class UserOnboardingController extends Controller
{


    public function __construct(UserOnboardingService $service)
    {

        $userOnboardingEnabled = (bool)config('user-onboarding.enable');

        if (! $userOnboardingEnabled) return abort(404);

        $this->middleware('guest')->except([
            //
        ]);

        $this->service = $service;

    }


    public function startUserOnboardingProcess(Request $request)
    {

        // Check process exists
        if ($this->service->checkOnboardingProcessExists()) {

            $route = $this->service->getNextProcessRoute();

            if (isset($route['flash'])) {
                return redirect()->route($route['route'])->with([
                    'flash' => $route['flash'],
                ]);
            } else {
                return redirect()->route($route['route']);
            }

        } else {

            $this->service->startNewOnboardingProcess();

        }

        return $this->showPersonalDataView($request);

    }


    public function continueUserOnboardingProcess(Request $request)
    {

        $id = decrypt($request->process);

        $this->service->continueProcess($id);

        // Check process exists
        if ($this->service->checkOnboardingProcessExists()) {

            $route = $this->service->getNextProcessRoute();

            if (isset($route['flash'])) {
                return redirect()->route($route['route'])->with([
                    'flash' => $route['flash'],
                ]);
            } else {
                return redirect()->route($route['route']);
            }

        } else {

            $this->service->startNewOnboardingProcess();

        }

        return $this->showPersonalDataView($request);

    }


    public function showPersonalDataView(Request $request)
    {

        if (! $this->service->checkOnboardingProcessExists()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        $form = new PersonalDataForm();

        return view('user-onboarding.personal-data')->with([
            'form' => $form,
        ]);

    }


    public function submitPersonalData(Request $request)
    {

        if (! $this->service->checkOnboardingProcessExists()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        $form = (new PersonalDataForm())->parseFormData($request);

        $validator = $this->service->validatePersonalData($form);

        if ($validator->fails()) return redirect()->back()->with([
            'flash' => $this->flashMessage('error', $validator->errors()->first()),
        ]);

        try {

            $this->service->submitPersonalData($form);

        } catch (\Exception $e) {

            return redirect()->back()->with([
                'flash' => $this->flashMessage('error', 'There was a critical error submitting your data.'),
            ]);

        }

        return redirect()->route($this->service::ROUTE__FIRM_ACCESS);

    }


    public function showFirmAccessView(Request $request)
    {

        if (! $this->service->checkOnboardingProcessExists()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if (! $this->service->verifyPersonalDataIsCaptured()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        return view('user-onboarding.firm-access');

    }


    public function showFirmRequestAccessView(Request $request)
    {

        if (! $this->service->checkOnboardingProcessExists()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if (! $this->service->verifyPersonalDataIsCaptured()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if ($this->service->checkFirmAccessRequestExists()) {

            if ($this->service->checkFirmAccessRequestIsApproved()) {
                return redirect()->route($this->service::ROUTE__SET_PASSWORD);
            }

            if ($this->service->checkFirmAccessRequestIsPending()) {
                return redirect()->route($this->service::ROUTE__FIRM_REQUEST_PENDING)->with([
                    'flash' => $this->flashMessage('error', 'Firm access request already pending decision.'),
                ]);
            }

        }

        $form = new FirmRequestAccessForm();

        return view('user-onboarding.firm-request-access')->with([
            'form' => $form,
        ]);

    }


    public function submitFirmRequestAccess(Request $request)
    {

        if (! $this->service->checkOnboardingProcessExists()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if (! $this->service->verifyPersonalDataIsCaptured()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if ($this->service->checkFirmAccessRequestExists()) {

            if ($this->service->checkFirmAccessRequestIsApproved()) {
                return redirect()->route($this->service::ROUTE__SET_PASSWORD);
            }

            if ($this->service->checkFirmAccessRequestIsPending()) {
                return redirect()->route($this->service::ROUTE__FIRM_REQUEST_PENDING)->with([
                    'flash' => $this->flashMessage('error', 'Firm access request already pending decision.'),
                ]);
            }

        }

        $form = (new FirmRequestAccessForm())->parseFormData($request);

        $validator = $this->service->validateFirmAccessRequestData($form);

        if ($validator->fails()) return redirect()->back()->with([
            'flash' => $this->flashMessage('error', $validator->errors()->first()),
        ]);


        try {

            $this->service->submitFirmAccessRequest($request->all());

        } catch (\Exception $e) {

            return redirect()->back()->with([
                'flash' => $this->flashMessage('error', 'There was a critical error submitting your data.'),
            ]);

        }

        return redirect()->route($this->service::ROUTE__FIRM_REQUEST_PENDING);


    }


    public function showRequestAccessPendingView(Request $request)
    {

        if (! $this->service->checkOnboardingProcessExists()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if (! $this->service->verifyPersonalDataIsCaptured()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if (! $this->service->verifyFirmAccessRequestDataIsCaptured()) {
            return redirect()->route($this->service::ROUTE__FIRM_REQUEST_ACCESS);
        }

        if ($this->service->checkFirmAccessRequestExists()) {

            if ($this->service->checkFirmAccessRequestIsApproved()) {
                return redirect()->route($this->service::ROUTE__SET_PASSWORD);
            }

        }


        return view('user-onboarding.firm-request-pending');

    }


    public function showFirmRegisterView(Request $request)
    {

        if (! $this->service->checkOnboardingProcessExists()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if (! $this->service->verifyPersonalDataIsCaptured()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if ($this->service->checkFirmRegisterRequestExists()) {

            if ($this->service->checkFirmRegisterRequestIsApproved()) {
                return redirect()->route($this->service::ROUTE__SET_PASSWORD);
            }

            if ($this->service->checkFirmRegisterRequestIsPending()) {
                return redirect()->route($this->service::ROUTE__FIRM_REGISTER_PENDING)->with([
                    'flash' => $this->flashMessage('error', 'Firm register request already pending decision.'),
                ]);
            }

        }

        $form = new FirmRegisterForm();

        return view('user-onboarding.firm-register')->with([
            'form' => $form,
        ]);

    }


    public function submitFirmRegister(Request $request)
    {

        if (! $this->service->checkOnboardingProcessExists()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if (! $this->service->verifyPersonalDataIsCaptured()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if ($this->service->checkFirmRegisterRequestExists()) {

            if ($this->service->checkFirmRegisterRequestIsApproved()) {
                return redirect()->route($this->service::ROUTE__SET_PASSWORD);
            }

            if ($this->service->checkFirmRegisterRequestIsPending()) {
                return redirect()->route($this->service::ROUTE__FIRM_REGISTER_PENDING)->with([
                    'flash' => $this->flashMessage('error', 'Firm register request already pending decision.'),
                ]);
            }

        }

        $form = (new FirmRegisterForm())->parseFormData($request);

        $validator = $this->service->validateFirmRegisterRequestData($form);

        if ($validator->fails()) return redirect()->back()->with([
            'flash' => $this->flashMessage('error', $validator->errors()->first()),
        ]);


        try {

            $this->service->submitFirmRegisterRequest($request->all());

        } catch (\Exception $e) {

            return redirect()->back()->with([
                'flash' => $this->flashMessage('error', 'There was a critical error submitting your data.'),
            ]);

        }

        return redirect()->route($this->service::ROUTE__FIRM_REGISTER_PENDING);

    }


    public function showRegisterPendingView(Request $request)
    {

        if (! $this->service->checkOnboardingProcessExists()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if (! $this->service->verifyPersonalDataIsCaptured()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if (! $this->service->verifyFirmRegisterDataIsCaptured()) {
            return redirect()->route($this->service::ROUTE__FIRM_REGISTER);
        }

        if ($this->service->checkFirmRegisterRequestExists()) {

            if ($this->service->checkFirmRegisterRequestIsApproved()) {
                return redirect()->route($this->service::ROUTE__SET_PASSWORD);
            }

        }


        return view('user-onboarding.firm-register-pending');

    }


    public function showSetPasswordView(Request $request)
    {

        if (! $this->service->checkOnboardingProcessExists()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if (! $this->service->verifyUserAccountIsCreated()) {
            return redirect()->route($this->service::ROUTE__START);
        }


        return view('user-onboarding.set-password');

    }


    public function submitSetPassword(Request $request)
    {

        if (! $this->service->checkOnboardingProcessExists()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if (! $this->service->verifyUserAccountIsCreated()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        $password = $request->get('password');

        $validator = $this->service->validatePassword($password);

        if ($validator->fails()) return redirect()->back()->with([
            'flash' => $this->flashMessage('error', $validator->errors()->first()),
        ]);

        $this->service->submitPassword($password);

        return redirect()->route($this->service::ROUTE__GET_STARTED);

    }


    public function showGetStartedView(Request $request)
    {

        if (! $this->service->checkOnboardingProcessExists()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if (! $this->service->verifyUserAccountIsCreated()) {
            return redirect()->route($this->service::ROUTE__START);
        }


        return view('user-onboarding.get-started');

    }


    public function submitGetStarted(Request $request)
    {

        if (! $this->service->checkOnboardingProcessExists()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        if (! $this->service->verifyUserAccountIsCreated()) {
            return redirect()->route($this->service::ROUTE__START);
        }

        $this->service->completeProcess();

        return redirect()->route($this->service::ROUTE__DASHBOARD);


    }

}
