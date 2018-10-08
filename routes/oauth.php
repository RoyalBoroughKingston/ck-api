<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| OAuth Routes
|--------------------------------------------------------------------------
|
| Here is where you can register OAuth routes for your application. These
| routes extend upon the OAuth 2.0 standard and as such, are bespoke
| to this application.
|
*/

Route::post('/oauth/logout', 'Oauth\\LogoutController');
