@extends('user-onboarding.base')

@push('foot-scripts')
@include('app.includes.vue-form-generator-scripts')
@endpush

@section('content')

    @include('user-onboarding.includes.steps', [
        'stepCount' => 3,
        'currentStep' => 1,
    ])


    <h1 class="step-heading">
        Your Information
    </h1>

    <p class="step-desc">
        Please provide your basic information to get started.
    </p>


    <form class="step-form step-form--personal-data" action="{{ route('user-onboarding.personal-data.submit') }}" method="POST">

        @csrf

        {!! $form->render() !!}

        <button class="btn btn--secondary" type="submit">Next</button>

    </form>


@endsection
