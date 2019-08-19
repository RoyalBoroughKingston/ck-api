<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::namespace('Auth')->group(function () {
    // Authentication Routes.
    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login');
    Route::get('login/code', 'LoginController@showOtpForm')->name('login.code');
    Route::post('login/code', 'LoginController@otp');
    Route::post('logout', 'LoginController@logout')->name('logout');

    // Password Reset Routes.
    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');
});

Route::get('/', 'HomeController')->name('home');

Route::get('/docs', 'DocsController@index')
    ->name('docs.index');

Route::get('/docs/openapi.json', 'DocsController@show')
    ->name('docs.show');
