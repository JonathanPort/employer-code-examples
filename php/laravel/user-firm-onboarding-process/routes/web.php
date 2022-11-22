<?php

use App\Http\Controllers\Web\UserOnboardingController;
use Illuminate\Support\Facades\Route;

/**
 * User onboarding
 */
Route::get('/user-onboarding/start', [UserOnboardingController::class, 'startUserOnboardingProcess'])
     ->name('user-onboarding.start');

Route::get('/user-onboarding/continue', [UserOnboardingController::class, 'continueUserOnboardingProcess'])
     ->name('user-onboarding.continue');

Route::get('/user-onboarding/personal-data', [UserOnboardingController::class, 'showPersonalDataView'])
     ->name('user-onboarding.personal-data');

Route::post('/user-onboarding/personal-data', [UserOnboardingController::class, 'submitPersonalData'])
     ->name('user-onboarding.personal-data.submit');

Route::get('/user-onboarding/firm-access', [UserOnboardingController::class, 'showFirmAccessView'])
     ->name('user-onboarding.firm-access');

Route::get('/user-onboarding/firm-request-access', [UserOnboardingController::class, 'showFirmRequestAccessView'])
     ->name('user-onboarding.firm-request-access');

Route::post('/user-onboarding/firm-request-access', [UserOnboardingController::class, 'submitFirmRequestAccess'])
     ->name('user-onboarding.firm-request-access.submit');

Route::get('/user-onboarding/firm-request-pending', [UserOnboardingController::class, 'showRequestAccessPendingView'])
     ->name('user-onboarding.firm-request-pending');

Route::get('/user-onboarding/firm-register', [UserOnboardingController::class, 'showFirmRegisterView'])
     ->name('user-onboarding.firm-register');

Route::post('/user-onboarding/firm-register', [UserOnboardingController::class, 'submitFirmRegister'])
     ->name('user-onboarding.firm-register.submit');

Route::get('/user-onboarding/firm-register-pending', [UserOnboardingController::class, 'showRegisterPendingView'])
     ->name('user-onboarding.firm-register-pending');

Route::get('/user-onboarding/set-password', [UserOnboardingController::class, 'showSetPasswordView'])
     ->name('user-onboarding.set-password');

Route::post('/user-onboarding/set-password', [UserOnboardingController::class, 'submitSetPassword'])
     ->name('user-onboarding.set-password.submit');

Route::get('/user-onboarding/get-started', [UserOnboardingController::class, 'showGetStartedView'])
     ->name('user-onboarding.get-started');

Route::post('/user-onboarding/get-started', [UserOnboardingController::class, 'submitGetStarted'])
     ->name('user-onboarding.get-started.submit');
