<?php

return [

    /*
     * The number of results to show in paginated responses.
     */
    'pagination_results' => 10,

    /*
     * The API key to use with the Google Geocoding API.
     */
    'google_api_key' => env('GOOGLE_API_KEY'),

    /*
     * Available drivers: 'stub', 'nominatim', 'google'
     */
    'geocode_driver' => env('GEOCODE_DRIVER', 'stub'),

    /*
     * Used for GOV.UK Notify.
     */
    'email_templates' => [

        'referral_created' => [
            'notify_client' => 'unique-template-id', // TODO: Get the actual values for the templates.
        ],

    ],

];
