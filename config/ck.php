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
        'referral_completed' => [
            'notify_client' => [
                'email' => '6cde8dcc-3458-4190-a42a-a2a62fcbb37e',
                'sms' => '33d7f0a0-dfe3-41ff-b7a3-a16e570317f2',
            ],
        ],
        'referral_incompleted' => [
            'notify_client' => [
                'email' => '69e6a891-21e1-402b-8670-71b651218507',
                'sms' => 'b9d203fa-2db1-4baf-aa1a-0ef7a8a8ecdc',
            ],
        ],
        'page_feedback_received' => [
            'notify_global_admin' => [
                'email' => '55af8b8f-1b36-4854-8cce-07d998fdd82a',
            ],
        ],
        'update_request_submitted' => [
            'notify_submitter' => [
                'email' => 'e6cd56cc-6259-4cc7-9568-7c48a4988abc',
            ],
            'notify_global_admin' => [
                'email' => '9d6c6177-37af-47f0-b097-bffd430a48cc',
            ],
        ],
        'update_request_approved' => [
            'notify_submitter' => [
                'email' => 'e26647a6-97e9-4ab0-bedd-e924b4d03742',
            ],
        ],
        'update_request_rejected' => [
            'notify_submitter' => [
                'email' => '4b9a76de-f869-4327-8563-51ebdf9d13f6',
            ],
        ],
        'user_created' => [
            'notify_user' => [
                'email' => '7bb4074d-ffe8-4c80-baac-fbeb92be3ef9',
            ],
        ],
    ],

];
