<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body>
    <header class="govuk-header" role="banner" data-module="header">
        <div class="govuk-header__container govuk-width-container">

            <div class="govuk-header__logo">
                <a href="{{ route('home') }}" class="govuk-header__link govuk-header__link--homepage">
                    <span class="govuk-header__logotype">
                      <img src="/images/ck-logo-horizontal-white.png" class="govuk-header__logotype-crown" alt="Connected Kingston logo" title="Connected Kingston">
                    </span>
                </a>
            </div>

            <div class="govuk-header__content">
                <a href="{{ route('home') }}" class="govuk-header__link govuk-header__link--service-name">
                    {{ config('app.name') }}
                </a>
            </div>

        </div>
    </header>

    <div class="govuk-width-container">
        <main class="govuk-main-wrapper " id="main-content" role="main">
            @yield('content')
        </main>
    </div>
</body>
</html>
