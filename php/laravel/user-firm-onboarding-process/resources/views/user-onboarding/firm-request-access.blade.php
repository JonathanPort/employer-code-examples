@extends('user-onboarding.base')

@push('foot-scripts')
@include('app.includes.vue-form-generator-scripts')
@endpush

@section('content')

    @include('user-onboarding.includes.steps', [
        'stepCount' => 3,
        'currentStep' => 2,
    ])


    <h1 class="step-heading">
        Your Firm
    </h1>

    <p class="step-desc">
        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.
    </p>


    <form class="step-form" action="{{ route('user-onboarding.firm-request-access.submit') }}" method="POST">

        @csrf

        {!! $form->render() !!}

        <button class="btn btn--secondary" type="submit">Next</button>

    </form>


@endsection
