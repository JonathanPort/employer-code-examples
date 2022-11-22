@extends('app.email.templates.default')

@section('content')

    @include('app.email.includes.greeting', [
        'who' => $model->first_name,
    ])

    @include('app.email.includes.paragraph', [
        'content' => 'Your request to create ' . $model->company_name . ' has been declined.'
    ])

    @include('app.email.includes.signature')

@endsection
