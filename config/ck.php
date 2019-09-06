<?php

return [

    /*
     * The default number of results to show in paginated responses.
     */
    'pagination_results' => 25,

    /*
     * The maximum number of results that can be requested.
     */
    'max_pagination_results' => 100,

    /*
     * Available drivers: 'stub', 'nominatim', 'google'
     */
    'geocode_driver' => env('GEOCODE_DRIVER', 'stub'),

    /*
     * The API key to use with the Google Geocoding API.
     */
    'google_api_key' => env('GOOGLE_API_KEY'),

    /*
     * Available drivers: 'log', 'null', 'gov'
     */
    'email_driver' => env('EMAIL_DRIVER', 'log'),

    /*
     * Available drivers: 'log', 'null', 'gov'
     */
    'sms_driver' => env('SMS_DRIVER', 'log'),

    /*
     * The GOV.UK Notify API key.
     */
    'gov_notify_api_key' => env('GOV_NOTIFY_API_KEY'),

    /*
     * The contact details for the global admin team.
     */
    'global_admin' => [
        'email' => env('GLOBAL_ADMIN_EMAIL'),
    ],

    /*
     * The URI for the backend app.
     */
    'backend_uri' => env('BACKEND_URI', ''),

    /*
     * The number of working days a service must respond within.
     */
    'working_days_for_service_to_respond' => 10,

    /*
     * If one time password authentication should be enabled.
     */
    'otp_enabled' => env('OTP_ENABLED', true),

    /*
     * The distance (in miles) that the search results should limit up to.
     */
    'search_distance' => 15,

    /*
     * The dimensions to automatically generate resized images at.
     */
    'cached_image_dimensions' => [
        150,
        350,
    ],

    /*
     * Used for GOV.UK Notify.
     */
    'notifications_template_ids' => [
        'password_reset' => [
            'email' => '5018a240-1c8e-4cb9-9322-f4f465c4500f',
        ],
        'otp_login_code' => [
            'sms' => '7b86b267-d0c9-434f-843d-c05b4d065c9a',
        ],
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
        'referral_still_unactioned' => [
            'notify_global_admin' => [
                'email' => 'd13651c2-93fd-4ace-8a19-2e83f7e30ca1',
            ],
        ],
        'referral_completed' => [
            'notify_client' => [
                'email' => '6cde8dcc-3458-4190-a42a-a2a62fcbb37e',
                'sms' => '33d7f0a0-dfe3-41ff-b7a3-a16e570317f2',
            ],
            'notify_referee' => [
                'email' => 'ccdab7ea-4a16-4add-a4b0-bdb8f056b31d',
                'sms' => 'adf7ea2f-8ebc-4429-baf9-c15eece24e1f',
            ],
        ],
        'referral_incompleted' => [
            'notify_client' => [
                'email' => '69e6a891-21e1-402b-8670-71b651218507',
                'sms' => 'b9d203fa-2db1-4baf-aa1a-0ef7a8a8ecdc',
            ],
            'notify_referee' => [
                'email' => '2fee9310-d1d5-4cd2-8b2e-88a03fabf111',
                'sms' => '4429932d-fbe7-401e-9b30-7d15df08647c',
            ],
        ],
        'page_feedback_received' => [
            'notify_global_admin' => [
                'email' => '55af8b8f-1b36-4854-8cce-07d998fdd82a',
            ],
        ],
        'update_request_received' => [
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
        'user_roles_updated' => [
            'notify_user' => [
                'email' => '9018cd8d-46fd-47e7-baa3-1ac6d3b6d943',
            ],
        ],
        'service_created' => [
            'notify_global_admin' => [
                'email' => 'e659e10a-f7b3-4f86-a5fa-289cfef1cf3f',
            ],
        ],
        'service_update_prompt' => [
            'notify_service_admin' => [
                'email' => '647ab603-26c9-4620-a1ad-ab91909bd605',
            ],
            'notify_global_admin' => [
                'email' => 'd4a30c4f-e32b-4b69-ad19-ce7bd6c70e81',
            ],
        ],
        'stale_service_disabled' => [
            'notify_global_admin' => [
                'email' => 'e1686c8c-926e-4ec3-bf1f-4ff906e93b76',
            ],
        ],
        'scheduled_report_generated' => [
            'notify_global_admin' => [
                'email' => 'fa6f064a-6b63-4dc0-a83d-602e9cbb5bfe',
            ],
        ],
        'organisation_sign_up_form_received' => [
            'notify_submitter' => [
                'email' => 'a6834de1-7144-4183-b3ce-edf481ef6953',
            ],
            'notify_global_admin' => [
                'email' => '0eb7828f-b28f-4c0c-acbe-62c7fbcc0be7',
            ],
        ],
        'organisation_sign_up_form_approved' => [
            'notify_submitter' => [
                'email' => '18815d90-b2b9-4205-9784-f0e05e801599',
            ],
        ],
        'organisation_sign_up_form_rejected' => [
            'notify_submitter' => [
                'email' => '6575fea8-5517-41fa-b78d-0c77cdc5d533',
            ],
        ],
    ],

];
