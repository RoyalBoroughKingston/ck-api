@extends('layout')

@section('content')
<div class="govuk-grid-row">
    <div class="govuk-grid-column-two-thirds">
        <h1 class="govuk-heading-xl">Connected Kingston API</h1>

        <p class="govuk-body">Click here to go to the <a class="govuk-link govuk-link--no-visited-state" href="{{ backend_uri() }}">Admin Portal</a>.</p>

        <p class="govuk-body">Click here to go to the <a class="govuk-link govuk-link--no-visited-state" href="{{ route('docs.index') }}">API documentation</a>.</p>

        @guest
            <p class="govuk-body">Click here to <a class="govuk-link govuk-link--no-visited-state" href="{{ route('login') }}">Login</a>.</p>
        @else
            <p class="govuk-body">Click here to <a class="govuk-link govuk-link--no-visited-state gov-link--logout" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit()">Logout</a>.</p>
            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                @csrf
            </form>
        @endguest
    </div>
</div>
@endsection
