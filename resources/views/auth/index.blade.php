@extends('auth.layout')

@section('content')
<div class="govuk-grid-row">
    <div class="govuk-grid-column-two-thirds">
        <h1 class="govuk-heading-xl">Connected Kingston API</h1>

        <ul class="govuk-list">
            <li>
                Click here to go to the <a class="govuk-link" href="{{ backend_uri()  }}">Admin Portal</a>.
            </li>
            @guest
            <li>
                Click here to <a class="govuk-link" href="{{ route('login') }}">Login</a>.
            </li>
            @else
            <li>
                Click here to <a class="govuk-link gov-link--logout" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit()">Logout</a>.
                <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                    @csrf
                </form>
            </li>
            @endguest
        </ul>
    </div>
</div>
@endsection
