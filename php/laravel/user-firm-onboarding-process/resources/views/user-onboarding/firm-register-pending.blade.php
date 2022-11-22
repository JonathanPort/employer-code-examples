@extends('user-onboarding.base')


@section('content')

    @include('user-onboarding.includes.steps', [
        'stepCount' => 3,
        'currentStep' => 2,
    ])


    <h1 class="step-heading">
        Your Firm Register request is pending admin approval..
    </h1>

    <p class="step-desc">
        An admin will approve or decline your request within 48 hours. On approval, you will receive a notifiation email containing a link to continue into EXAMPLEAPPNAME.
    </p>


@endsection
