@extends('app.email.templates.default')

@section('content')

    @include('app.email.includes.greeting', [
        'who' => $model->first_name,
    ])

    @include('app.email.includes.paragraph', [
        'content' => 'Your request to create ' . $model->company_name . ' has been approved.'
    ])

    @include('app.email.includes.btn', [
        'text' => 'Continue Registration',
        'link' => route('user-onboarding.continue', ['process' => $model->encrypted_id]),
    ])

    @include('app.email.includes.signature')

@endsection
