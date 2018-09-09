@extends('auth.layout')

@section('content')
<div class="govuk-grid-row">
    <div class="govuk-grid-column-one-half">
        <h1 class="govuk-heading-xl">Login</h1>
        <form method="POST" action="{{ route('login.code') }}" novalidate>

            @csrf

            <div class="govuk-form-group {{ $errors->has('token') ? 'govuk-form-group--error' : '' }}">
                <label class="govuk-label govuk-label--m" for="token">
                    Authorisation code
                </label>
                @if($errors->has('token'))
                <span class="govuk-error-message">
                    {{ $errors->first('token') }}
                </span>
                @endif
                <input class="govuk-input" id="token" name="token" type="password" aria-describedby="token-hint">
            </div>

            <button type="submit" class="govuk-button">
                Login
            </button>

        </form>
    </div>
</div>
@endsection
