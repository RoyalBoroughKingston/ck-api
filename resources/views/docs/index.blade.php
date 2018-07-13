<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} API Documentation</title>
    <link rel="stylesheet" href="{{ mix('/css/docs.css') }}">
</head>
<body>
    <div class="app">
        <nav class="sidebar">
            <ul>
                <li><a class="sidebar-link" href="#core-api">Core API</a></li>
                <li><a class="sidebar-link" href="#open-referral-api">Open Referral API</a></li>
            </ul>
        </nav>

        <main class="swagger-ui">
            <div id="core-api" class="swagger-ui__container"></div>
            <div id="open-referral-api" class="swagger-ui__container hidden"></div>
        </main>
    </div>

    <script src="{{ mix('/js/docs.js') }}"></script>
</body>
</html>
