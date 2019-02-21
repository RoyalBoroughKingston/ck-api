<?php

use Illuminate\Support\Facades\Route;

Route::get('oauth/authorize', 'App\Http\Controllers\Passport\AuthorizationController@authorize');
Route::post('oauth/authorize', 'Laravel\Passport\Http\Controllers\ApproveAuthorizationController@approve');
Route::delete('oauth/authorize', 'Laravel\Passport\Http\Controllers\DenyAuthorizationController@deny');
