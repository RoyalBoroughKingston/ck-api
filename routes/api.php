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

Route::get('oauth/clients', 'Passport\\ClientController@index');

Route::prefix('core/v1')->namespace('Core\\V1')->name('core.v1.')->group(function () {
    // Audits.
    Route::match(['GET', 'POST'], '/audits/index', 'AuditController@index');
    Route::apiResource('/audits', 'AuditController')->only('index', 'show');

    // Collection Categories.
    Route::match(['GET', 'POST'], '/collections/categories/index', 'CollectionCategoryController@index');
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
    Route::match(['GET', 'POST'], '/collections/personas/index', 'CollectionPersonaController@index');
    Route::apiResource('/collections/personas', 'CollectionPersonaController')
        ->parameter('personas', 'collection')
        ->names([
            'index' => 'collection-personas.index',
            'store' => 'collection-personas.store',
            'show' => 'collection-personas.show',
            'update' => 'collection-personas.update',
            'destroy' => 'collection-personas.destroy',
        ]);
    Route::get('/collections/personas/{collection}/image.png', 'CollectionPersona\\ImageController')->name('collection-personas.image');

    // Files.
    Route::apiResource('/files', 'FileController')->only('store');

    // Locations.
    Route::match(['GET', 'POST'], '/locations/index', 'LocationController@index');
    Route::apiResource('/locations', 'LocationController');
    Route::get('/locations/{location}/image.png', 'Location\\ImageController')->name('locations.image');

    // Notifications.
    Route::match(['GET', 'POST'], '/notifications/index', 'NotificationController@index');
    Route::apiResource('/notifications', 'NotificationController')->only('index', 'show');

    // Organisations.
    Route::match(['GET', 'POST'], '/organisations/index', 'OrganisationController@index');
    Route::apiResource('/organisations', 'OrganisationController');
    Route::get('/organisations/{organisation}/logo.png', 'Organisation\\LogoController')->name('organisations.logo');

    // Page Feedbacks.
    Route::match(['GET', 'POST'], '/page-feedbacks/index', 'PageFeedbackController@index');
    Route::apiResource('/page-feedbacks', 'PageFeedbackController')->only('index', 'store', 'show');

    // Referrals.
    Route::match(['GET', 'POST'], '/referrals/index', 'ReferralController@index');
    Route::apiResource('/referrals', 'ReferralController');

    // Report Schedules.
    Route::match(['GET', 'POST'], '/report-schedules/index', 'ReportScheduleController@index');
    Route::apiResource('/report-schedules', 'ReportScheduleController');

    // Reports.
    Route::match(['GET', 'POST'], '/reports/index', 'ReportController@index');
    Route::apiResource('/reports', 'ReportController')->only('index', 'store', 'show', 'destroy');
    Route::get('/reports/{report}/download', 'Report\\DownloadController@show')->name('reports.download');

    // Search.
    Route::post('/search', 'SearchController')->name('search');

    // Service Locations.
    Route::match(['GET', 'POST'], '/service-locations/index', 'ServiceLocationController@index');
    Route::apiResource('/service-locations', 'ServiceLocationController');
    Route::get('/service-locations/{service_location}/image.png', 'ServiceLocation\\ImageController')->name('service-locations.image');

    // Services.
    Route::match(['GET', 'POST'], '/services/index', 'ServiceController@index');
    Route::apiResource('/services', 'ServiceController');
    Route::get('/services/{service}/related', 'Service\\RelatedController')->name('services.related');
    Route::get('/services/{service}/logo.png', 'Service\\LogoController')->name('services.logo');
    Route::get('/services/{service}/gallery-items/{file}', 'Service\\GalleryItemController')->name('services.gallery-items');

    // Status Updates.
    Route::match(['GET', 'POST'], '/status-updates/index', 'StatusUpdateController@index');
    Route::apiResource('/status-updates', 'StatusUpdateController');

    // Stop words.
    Route::get('/stop-words', 'StopWordsController@index')->name('stop-words.index');
    Route::put('/stop-words', 'StopWordsController@update')->name('stop-words.update');

    // Taxonomy Categories.
    Route::match(['GET', 'POST'], '/taxonomies/categories/index', 'TaxonomyCategoryController@index');
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
    Route::match(['GET', 'POST'], '/taxonomies/organisations/index', 'TaxonomyOrganisationController@index');
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
    Route::match(['GET', 'POST'], '/update-requests/index', 'UpdateRequestController@index');
    Route::apiResource('/update-requests', 'UpdateRequestController')->only('index', 'show', 'destroy');
    Route::put('/update-requests/{update_request}/approve', 'UpdateRequest\\ApproveController@update')->name('update-requests.approve');

    // Users.
    Route::match(['GET', 'POST'], '/users/index', 'UserController@index');
    Route::get('/users/user', 'UserController@user')->name('users.user');
    Route::delete('/users/user/sessions', 'User\\SessionController@destroy')->name('users.user.sessions.destroy');
    Route::apiResource('/users', 'UserController');
});
