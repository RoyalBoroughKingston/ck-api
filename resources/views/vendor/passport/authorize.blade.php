@extends('layout')

@section('content')
    <div class="govuk-grid-row">
        <div class="govuk-grid-column-one-half">

            <h1 class="govuk-heading-xl">Authorisation request</h1>

            <p class="govuk-body">{{ $client->name }} is requesting permission to access your account.</p>

            <form method="POST" action="{{ url('/oauth/authorize') }}" novalidate style="display: inline-block;">

                @csrf
                {{ method_field('DELETE') }}

                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->id }}">

                <button type="submit" class="govuk-button govuk-button--error">
                    Cancel
                </button>

            </form>

            <form method="POST" action="{{ url('/oauth/authorize') }}" novalidate style="display: inline-block;">

                @csrf

                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->id }}">

                <button type="submit" class="govuk-button">
                    Authorise
                </button>

            </form>

        </div>
    </div>
@endsection
