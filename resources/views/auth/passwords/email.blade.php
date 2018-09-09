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

{{--<div class="container">--}}
    {{--<div class="row justify-content-center">--}}
        {{--<div class="col-md-8">--}}
            {{--<div class="card">--}}
                {{--<div class="card-header">{{ __('Reset Password') }}</div>--}}

                {{--<div class="card-body">--}}
                    {{--@if (session('status'))--}}
                        {{--<div class="alert alert-success" role="alert">--}}
                            {{--{{ session('status') }}--}}
                        {{--</div>--}}
                    {{--@endif--}}

                    {{--<form method="POST" action="{{ route('password.email') }}">--}}
                        {{--@csrf--}}

                        {{--<div class="form-group row">--}}
                            {{--<label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>--}}

                            {{--<div class="col-md-6">--}}
                                {{--<input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>--}}

                                {{--@if ($errors->has('email'))--}}
                                    {{--<span class="invalid-feedback" role="alert">--}}
                                        {{--<strong>{{ $errors->first('email') }}</strong>--}}
                                    {{--</span>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="form-group row mb-0">--}}
                            {{--<div class="col-md-6 offset-md-4">--}}
                                {{--<button type="submit" class="btn btn-primary">--}}
                                    {{--{{ __('Send Password Reset Link') }}--}}
                                {{--</button>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</form>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
@endsection
