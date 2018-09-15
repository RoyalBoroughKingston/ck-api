@extends('auth.layout')

@section('content')
<div class="govuk-grid-row">
    <div class="govuk-grid-column-one-half">
        <h1 class="govuk-heading-xl">Forgotten password</h1>

        @if (session('status'))
        <div class="govuk-panel govuk-panel--confirmation">
            <div class="govuk-panel__body">
                {{ session('status') }}
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" novalidate>

            @csrf

            <div class="govuk-form-group {{ $errors->has('email') ? 'govuk-form-group--error' : '' }}">
                <label class="govuk-label govuk-label--m" for="email">
                    Email
                </label>
                <span id="email-number-hint" class="govuk-hint">
                    The email address you use to login to your {{ config('app.name') }} account with.
                </span>
                @if($errors->has('email'))
                <span class="govuk-error-message">
                    {{ $errors->first('email') }}
                </span>
                @endif
                <input class="govuk-input" id="email" name="email" type="email" aria-describedby="email-hint" value="{{ old('email') }}">
            </div>

            <button type="submit" class="govuk-button">
                Send password reset link
            </button>

        </form>
    </div>
</div>
@endsection
