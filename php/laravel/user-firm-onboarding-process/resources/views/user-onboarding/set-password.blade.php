@extends('user-onboarding.base')

@push('foot-scripts')
@include('app.includes.vue-form-generator-scripts')
@endpush

@section('content')

    @include('user-onboarding.includes.steps', [
        'stepCount' => 3,
        'currentStep' => 3,
    ])


    <h1 class="step-heading">
        Set your account password
    </h1>

    <p class="step-desc">
        Choose something unique and use a combination of characters to make it difficult to guess.
    </p>


    <form class="step-form"
          action="{{ route('user-onboarding.set-password.submit') }}"
          method="POST"
          style="max-width: 500px"
    >

        @csrf

        <div class="input">

            <input type="password"
                   name="password"
                   value=""
                   required
                   data-password-strength-input="account-pass"
                   placeholder="Enter new password"
            >

            <input type="hidden"
                   name="password_strength"
                   required
                   data-password-strength-hidden-input="account-pass"
            >

        </div>

        @include('app.includes.password-strength-metre', [
            'name' => 'account-pass',
        ])

        <br><br>

        <button class="btn btn--secondary" type="submit">Login to EXAMPLEAPPNAME</button>

    </form>


@endsection
