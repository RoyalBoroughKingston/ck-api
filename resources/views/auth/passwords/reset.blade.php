@extends('layout')

@section('content')
<div class="govuk-grid-row">
    <div class="govuk-grid-column-one-half">
        <h1 class="govuk-heading-xl">Reset password</h1>

        <p class="govuk-body">Enter your email address along with your new password.</p>

        <form method="POST" action="{{ route('password.update') }}" novalidate>

            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

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
            </div>

            <div class="govuk-form-group {{ $errors->has('password_confirmation') ? 'govuk-form-group--error' : '' }}">
                <label class="govuk-label govuk-label--m" for="password-confirmation">
                    Confirm password
                </label>
                @if($errors->has('password_confirmation'))
                    <span class="govuk-error-message">
                    {{ $errors->first('password_confirmation') }}
                </span>
                @endif
                <input class="govuk-input" id="password-confirmation" name="password_confirmation" type="password" aria-describedby="password-confirmation-hint">
            </div>

            <button type="submit" class="govuk-button">
                Reset password
            </button>

        </form>
    </div>
</div>
@endsection
