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

Route::get('/docs', 'DocsController@index')
    ->name('docs.index');

Route::get('/docs/{path}', 'DocsController@show')
    ->where('path', '.*(.yaml)')
    ->name('docs.show');
