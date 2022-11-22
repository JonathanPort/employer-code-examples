@extends('user-onboarding.base')

@section('content')

    <form class="get-started"
          action="{{ route('user-onboarding.get-started.submit') }}"
          method="POST">

        @csrf

        <img class="icon" src="{{ asset('images/icons/single-check-blue.png') }}" alt="">

        <h1 class="step-heading">
            Your account has been approved.
        </h1>

        <p class="step-desc">
            Thank you for your patience. Your account has been approved. Click "Get started" to continue into EXAMPLEAPPNAME.
        </p>

        <div class="btns">
            <button class="btn btn--secondary" type="submit">Get Started</button>
            <a class="btn btn--primary" href="#">View Manual</a>
        </div>

    </form>


@endsection
