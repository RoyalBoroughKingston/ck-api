<?php

return [

    /*
     * The number of results to show in paginated responses.
     */
    'pagination_results' => 10,

    /*
     * Available drivers: 'stub', 'nominatim', 'google'
     */
    'geocode_driver' => env('GEOCODE_DRIVER', 'stub'),

    /*
     * The API key to use with the Google Geocoding API.
     */
    'google_api_key' => env('GOOGLE_API_KEY'),

    /*
     * Available drivers: 'log', 'gov'
     */
    'email_driver' => env('EMAIL_DRIVER', 'log'),

    /*
     * Available drivers: 'log', 'gov'
     */
    'sms_driver' => env('SMS_DRIVER', 'log'),

    /*
     * The GOV.UK Notify API key.
     */
    'gov_notify_api_key' => env('GOV_NOTIFY_API_KEY'),

    /*
     * Used for GOV.UK Notify.
     */
    'notifications_template_ids' => [

        'referral_created' => [

            'notify_client' => [
                'email' => '7e46d4d4-ce3f-475f-9416-fa35f2d5a65f',
                'sms' => '8ab81261-3b84-4cc9-8c76-d9aa85aa6aaa',
            ],

            'notify_referee' => [
                'email' => '6b789487-da89-4bee-8061-8af31c84cbfe',
                'sms' => '354e712f-a435-4ac6-9b18-52018992fcaf',
            ],

            'notify_service' => [
                'email' => '8fc13a1c-1194-4a7e-849c-42412bf1ede7',
            ],
        ],

        'referral_unactioned' => [

            'notify_service' => [
                'email' => '30a5e55f-0e56-4387-9cf3-3eb4ed83b115',
            ],

        ],

    ],

];
