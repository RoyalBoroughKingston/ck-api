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
    Route::apiResource('/collections/categories', 'CollectionCategoryController')
        ->parameter('categories', 'collection')
        ->names([
            'index' => 'collection-categories.index',
            'store' => 'collection-categories.store',
            'show' => 'collection-categories.show',
            'update' => 'collection-categories.update',
            'destroy' => 'collection-categories.destroy',
        ]);

    // Collection Personas.
    Route::apiResource('/collections/personas', 'CollectionPersonaController')
        ->parameter('personas', 'collection')
        ->names([
            'index' => 'collection-personas.index',
            'store' => 'collection-personas.store',
            'show' => 'collection-personas.show',
            'update' => 'collection-personas.update',
            'destroy' => 'collection-personas.destroy',
        ]);
    Route::get('/collections/personas/{collection}/image.png', 'CollectionPersona\\ImageController')->name('collection-personas.image.show');

    // Locations.
    Route::apiResource('/locations', 'LocationController');

    // Notifications.
    Route::apiResource('/notifications', 'NotificationController')->only('index', 'show');

    // Organisations.
    Route::apiResource('/organisations', 'OrganisationController');
    Route::get('/organisations/{organisation}/logo.png', 'Organisation\\LogoController')->name('organisations.logo');

    // Page Feedbacks.
    Route::apiResource('/page-feedbacks', 'PageFeedbackController')->only('index', 'store', 'show');

    // Referrals.
    Route::apiResource('/referrals', 'ReferralController')->only('index', 'store', 'show', 'update');

    // Report Schedules.
    Route::apiResource('/report-schedules', 'ReportScheduleController');

    // Reports.
    Route::apiResource('/reports', 'ReportController')->only('index', 'store', 'show', 'destroy');
    Route::get('/reports/{report}/download', 'Report\\DownloadController@show')->name('reports.download');

    // Search.
    Route::post('/search', 'SearchController')->name('search');

    // Service Locations.
    Route::apiResource('/service-locations', 'ServiceLocationController');

    // Services.
    Route::apiResource('/services', 'ServiceController');
    Route::get('/services/{service}/logo.png', 'Service\\LogoController')->name('services.logo.show');

    // Status Updates.
    Route::apiResource('/status-updates', 'StatusUpdateController');

    // Stop words.
    Route::get('/stop-words', 'StopWordsController@index')->name('stop-words.index');
    Route::put('/stop-words', 'StopWordsController@update')->name('stop-words.update');

    // Taxonomy Categories.
    Route::apiResource('/taxonomies/categories', 'TaxonomyCategoryController')
        ->parameter('categories', 'taxonomy')
        ->names([
            'index' => 'taxonomy-categories.index',
            'store' => 'taxonomy-categories.store',
            'show' => 'taxonomy-categories.show',
            'update' => 'taxonomy-categories.update',
            'destroy' => 'taxonomy-categories.destroy',
        ]);

    // Taxonomy Organisations.
    Route::apiResource('/taxonomies/organisations', 'TaxonomyOrganisationController')
        ->parameter('organisations', 'taxonomy')
        ->names([
            'index' => 'taxonomy-organisations.index',
            'store' => 'taxonomy-organisations.store',
            'show' => 'taxonomy-organisations.show',
            'update' => 'taxonomy-organisations.update',
            'destroy' => 'taxonomy-organisations.destroy',
        ]);

    // Thesaurus.
    Route::get('/thesaurus', 'ThesaurusController@index')->name('thesaurus.index');
    Route::put('/thesaurus', 'ThesaurusController@update')->name('thesaurus.update');

    // Update Requests.
    Route::apiResource('/update-requests', 'UpdateRequestController')->only('index', 'show', 'destroy');
    Route::put('/update-requests/{update_request}/approve', 'UpdateRequest\\ApproveController@update')->name('update-requests.approve');

    // Users.
    Route::get('/users/user', 'UserController@user')->name('users.user');
    Route::delete('/users/user/sessions', 'User\\SessionController@destroy')->name('users.user.sessions.destroy');
    Route::apiResource('/users', 'UserController');
});
