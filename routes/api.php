<?php

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

    // Collection Personas.
    Route::apiResource('/collections/personas', 'CollectionPersonaController')->names([
        'index' => 'collection-personas.index',
        'store' => 'collection-personas.store',
        'show' => 'collection-personas.show',
        'update' => 'collection-personas.update',
        'destroy' => 'collection-personas.destroy',
    ]);
    Route::post('/collections/personas/{persona}/image', 'CollectionPersona\\ImageController@store')->name('collection-personas.image.store');
    Route::get('/collections/personas/{persona}/image', 'CollectionPersona\\ImageController@show')->name('collection-personas.image.show');
    Route::delete('/collections/personas/{persona}/image', 'CollectionPersona\\ImageController@destroy')->name('collection-personas.image.destroy');

    // Locations.
    Route::apiResource('/locations', 'LocationController');

    // Notifications.
    Route::apiResource('/notifications', 'NotificationController')->only('index', 'show');

    // Organisations.
    Route::apiResource('/organisations', 'OrganisationController');
    Route::post('/organisations/{organisation}/logo', 'Organisation\\LogoController@store')->name('organisations.logo.store');
    Route::get('/organisations/{organisation}/logo', 'Organisation\\LogoController@show')->name('organisations.logo.show');
    Route::delete('/organisations/{organisation}/logo', 'Organisation\\LogoController@destroy')->name('organisations.logo.destroy');

    // Page Feedbacks.
    Route::apiResource('/page-feedbacks', 'PageFeedbackController')->only('index', 'store', 'show');

    // Referrals.
    Route::apiResource('/referrals', 'ReferralController')->only('index', 'store', 'show', 'update');

    // Report Schedules.
    Route::apiResource('/report-schedules', 'ReportScheduleController');

    // Reports.
    Route::apiResource('/reports', 'ReportController')->only('index', 'store', 'show', 'destroy');
    Route::get('/reports/{report}/download', 'Report\\DownloadController@show')->name('reports.download.show');

    // Service Locations.
    Route::apiResource('/service-locations', 'ServiceLocationController');

    // Services.
    Route::apiResource('/services', 'ServiceController');
    Route::post('/services/{service}/logo', 'Service\\LogoController@store')->name('services.logo.store');
    Route::get('/services/{service}/logo', 'Service\\LogoController@show')->name('services.logo.show');
    Route::delete('/services/{service}/logo', 'Service\\LogoController@destroy')->name('services.logo.destroy');
    Route::post('/services/{service}/seo-image', 'Service\\SeoImageController@store')->name('services.seo-image.store');
    Route::get('/services/{service}/seo-image', 'Service\\SeoImageController@show')->name('services.seo-image.show');
    Route::delete('/services/{service}/seo-image', 'Service\\SeoImageController@destroy')->name('services.seo-image.destroy');

    // Status Updates.
    Route::apiResource('/status-updates', 'StatusUpdateController');

    // Taxonomy Categories.
    Route::apiResource('/taxonomies/categories', 'TaxonomyCategoryController')->names([
        'index' => 'taxonomy-categories.index',
        'store' => 'taxonomy-categories.store',
        'show' => 'taxonomy-categories.show',
        'update' => 'taxonomy-categories.update',
        'destroy' => 'taxonomy-categories.destroy',
    ]);

    // Taxonomy Organisations.
    Route::apiResource('/taxonomies/organisations', 'TaxonomyOrganisationController')->names([
        'index' => 'taxonomy-organisations.index',
        'store' => 'taxonomy-organisations.store',
        'show' => 'taxonomy-organisations.show',
        'update' => 'taxonomy-organisations.update',
        'destroy' => 'taxonomy-organisations.destroy',
    ]);
});
