<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('core/v1')->namespace('Core\\V1')->name('core.v1.')->group(function () {
    // Audits.
    Route::apiResource('/audits', 'AuditController')->only('index', 'show');

    // Collection Categories.
    Route::apiResource('/collections/categories', 'CollectionCategoryController')->names([
        'index' => 'collection-categories.index',
        'store' => 'collection-categories.store',
        'show' => 'collection-categories.show',
        'update' => 'collection-categories.update',
        'destroy' => 'collection-categories.destroy',
    ]);
});
