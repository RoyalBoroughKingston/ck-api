@extends('auth.layout')

@section('content')
<div class="govuk-grid-row">
    <div class="govuk-grid-column-one-half">
        <h1 class="govuk-heading-xl">Login</h1>
        <form method="POST" action="{{ route('login') }}" novalidate>

            @csrf

            <div class="govuk-form-group {{ $errors->has('email') ? 'govuk-form-group--error' : '' }}">
                <label class="govuk-label govuk-label--m" for="email">
                    Email
                </label>
                @if($errors->has('email'))
                <span class="govuk-error-message">
                    {{ $errors->first('email') }}
                </span>
                @endif
                <input class="govuk-input" id="email" name="email" type="email" aria-describedby="email-hint" value="{{ old('email') }}">
            </div>

            <div class="govuk-form-group {{ $errors->has('password') ? 'govuk-form-group--error' : '' }}">
                <label class="govuk-label govuk-label--m" for="password">
                    Password
                </label>
                @if($errors->has('password'))
                <span class="govuk-error-message">
                    {{ $errors->first('password') }}
                </span>
                @endif
                <input class="govuk-input" id="password" name="password" type="password" aria-describedby="password-hint">
                <a class="govuk-link govuk-link--no-visited-state" href="{{ route('password.request') }}">Forgotten password?</a>
            </div>

            <button type="submit" class="govuk-button">
                @if(config('ck.otp_enabled'))
                    Send code
                @else
                    Login
                @endif
            </button>

        </form>
    </div>
</div>
@endsection
