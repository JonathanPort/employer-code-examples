@extends('app.email.templates.default')

@section('content')

    @include('app.email.includes.greeting', [
        'who' => $model->first_name,
    ])

    @include('app.email.includes.paragraph', [
        'content' => 'Your request to join ' . $model->company_name . ' is pending approval from the firm admin. You will recieve a decision within 48 hours.'
    ])

    @include('app.email.includes.signature')

@endsection
