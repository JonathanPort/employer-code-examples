<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="@stack('html-class-list')">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="{{ asset('css/user-onboarding.css') }}">

    @include('app.includes.favicons')

    @stack('head-styles')
    @stack('head-scripts')
    @stack('head-meta')

</head>
<body class="theme--light">

    <div class="user-onboarding-layout">

        <div class="user-onboarding-layout__decor">
            <div class="user-onboarding-layout__decor-inner"></div>
        </div>

        <div class="user-onboarding-layout__content">

            <header class="user-onboarding-layout__header">

                <h1 class="user-onboarding-layout__heading">
                    Register for EXAMPLEAPPNAME access:
                </h1>

                <div class="user-onboarding-layout__logo">
                    @include('app.includes.svg', [
                        'filename' => 'brand/logos/logo'
                    ])
                </div>

            </header>

            @yield('content')

        </div>

    </div>

    @include('app.includes.notifications')

    @stack('foot-styles')

    <script src="{{ asset('js/user-onboarding.js') }}"></script>
    @stack('foot-scripts')

</body>
</html>
