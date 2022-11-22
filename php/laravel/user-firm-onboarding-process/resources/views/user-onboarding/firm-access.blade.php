@extends('user-onboarding.base')


@section('content')

    @include('user-onboarding.includes.steps', [
        'stepCount' => 3,
        'currentStep' => 2,
    ])


    <h1 class="step-heading">
        Your Firm
    </h1>

    <p class="step-desc">
        To work as a solicitor or mediator within EXAMPLEAPPNAME, you either need to be a member of a firm, or a firm administrator/owner. Please request access to an existing firm or register your own firm. You must be a UK based firm registered with company house.
    </p>


    <a href="{{ route('user-onboarding.firm-request-access') }}" class="btn btn--secondary">
        Request access
    </a>

    <a href="{{ route('user-onboarding.firm-register') }}" class="btn btn--secondary">
        Register firm
    </a>


@endsection
