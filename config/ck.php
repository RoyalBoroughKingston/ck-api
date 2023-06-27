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
            'email' => env('NOTIFICATION_TEMPLATE_PASSWORD_RESET_EMAIL', ''),
        ],
        'otp_login_code' => [
            'sms' => env('NOTIFICATION_TEMPLATE_OTP_LOGIN_SMS', ''),
        ],
        'referral_created' => [
            'notify_client' => [
                'email' => env('NOTIFICATION_TEMPLATE_REFERRAL_CREATED_NOTIFY_CLIENT_EMAIL', ''),
                'sms' => env('NOTIFICATION_TEMPLATE_REFERRAL_CREATED_NOTIFY_CLIENT_SMS', ''),
            ],
            'notify_referee' => [
                'email' => env('NOTIFICATION_TEMPLATE_REFERRAL_CREATED_NOTIFY_REFEREE_EMAIL', ''),
                'sms' => env('NOTIFICATION_TEMPLATE_REFERRAL_CREATED_NOTIFY_REFEREE_SMS', ''),
            ],
            'notify_service' => [
                'email' => env('NOTIFICATION_TEMPLATE_REFERRAL_CREATED_NOTIFY_SERVICE_EMAIL', ''),
            ],
        ],
        'referral_unactioned' => [
            'notify_service' => [
                'email' => env('NOTIFICATION_TEMPLATE_REFERRAL_UNACTIONED_NOTIFY_SERVICE_EMAIL', ''),
            ],
        ],
        'referral_still_unactioned' => [
            'notify_global_admin' => [
                'email' => env('NOTIFICATION_TEMPLATE_REFERRAL_STILL_UNACTIONED_NOTIFY_GLOBAL_ADMIN_EMAIL', ''),
            ],
        ],
        'referral_completed' => [
            'notify_client' => [
                'email' => env('NOTIFICATION_TEMPLATE_REFERRAL_COMPLETED_NOTIFY_CLIENT_EMAIL', ''),
                'sms' => env('NOTIFICATION_TEMPLATE_REFERRAL_COMPLETED_NOTIFY_CLIENT_SMS', ''),
            ],
            'notify_referee' => [
                'email' => env('NOTIFICATION_TEMPLATE_REFERRAL_COMPLETED_NOTIFY_REFEREE_EMAIL', ''),
                'sms' => env('NOTIFICATION_TEMPLATE_REFERRAL_COMPLETED_NOTIFY_REFEREE_SMS', ''),
            ],
        ],
        'referral_incompleted' => [
            'notify_client' => [
                'email' => env('NOTIFICATION_TEMPLATE_REFERRAL_INCOMPLETED_NOTIFY_CLIENT_EMAIL', ''),
                'sms' => env('NOTIFICATION_TEMPLATE_REFERRAL_INCOMPLETED_NOTIFY_CLIENT_SMS', ''),
            ],
            'notify_referee' => [
                'email' => env('NOTIFICATION_TEMPLATE_REFERRAL_INCOMPLETED_NOTIFY_REFEREE_EMAIL', ''),
                'sms' => env('NOTIFICATION_TEMPLATE_REFERRAL_INCOMPLETED_NOTIFY_REFEREE_SMS', ''),
            ],
        ],
        'page_feedback_received' => [
            'notify_global_admin' => [
                'email' => env('NOTIFICATION_TEMPLATE_PAGE_FEEDBACK_RECEIVED_NOTIFY_GLOBAL_ADMIN_EMAIL', ''),
            ],
        ],
        'update_request_received' => [
            'notify_submitter' => [
                'email' => env('NOTIFICATION_TEMPLATE_UPDATE_REQUEST_RECEIVED_NOTIFY_SUBMITTER_EMAIL', ''),
            ],
            'notify_global_admin' => [
                'email' => env('NOTIFICATION_TEMPLATE_UPDATE_REQUEST_RECEIVED_NOTIFY_GLOBAL_ADMIN_EMAIL', ''),
            ],
        ],
        'update_request_approved' => [
            'notify_submitter' => [
                'email' => env('NOTIFICATION_TEMPLATE_UPDATE_REQUEST_APPROVED_NOTIFY_SUBMITTER_EMAIL', ''),
            ],
        ],
        'update_request_rejected' => [
            'notify_submitter' => [
                'email' => env('NOTIFICATION_TEMPLATE_UPDATE_REQUEST_REJECTED_NOTIFY_SUBMITTER_EMAIL', ''),
            ],
        ],
        'user_created' => [
            'notify_user' => [
                'email' => env('NOTIFICATION_TEMPLATE_USER_CREATED_NOTIFY_USER_EMAIL', ''),
            ],
        ],
        'user_roles_updated' => [
            'notify_user' => [
                'email' => env('NOTIFICATION_TEMPLATE_USER_ROLES_UPDATED_NOTIFY_USER_EMAIL', ''),
            ],
        ],
        'service_created' => [
            'notify_global_admin' => [
                'email' => env('NOTIFICATION_TEMPLATE_SERVICE_CREATED_NOTIFY_GLOBAL_ADMIN_EMAIL', ''),
            ],
        ],
        'service_update_prompt' => [
            'notify_service_admin' => [
                'email' => env('NOTIFICATION_TEMPLATE_SERVICE_UPDATE_PROMPT_NOTIFY_SERVICE_ADMIN_EMAIL', ''),
            ],
            'notify_global_admin' => [
                'email' => env('NOTIFICATION_TEMPLATE_SERVICE_UPDATE_PROMPT_NOTIFY_GLOBAL_ADMIN_EMAIL', ''),
            ],
        ],
        'stale_service_disabled' => [
            'notify_global_admin' => [
                'email' => env('NOTIFICATION_TEMPLATE_STALE_SERVICE_DISABLED_NOTIFY_GLOBAL_ADMIN_EMAIL', ''),
            ],
        ],
        'scheduled_report_generated' => [
            'notify_global_admin' => [
                'email' => env('NOTIFICATION_TEMPLATE_SCHEDULED_REPORT_GENERATED_NOTIFY_GLOBAL_ADMIN_EMAIL', ''),
            ],
        ],
        'organisation_sign_up_form_received' => [
            'notify_submitter' => [
                'email' => env('NOTIFICATION_TEMPLATE_ORGANISATION_SIGNUP_RECEIVED_NOTIFY_SUBMITTER_EMAIL', ''),
            ],
            'notify_global_admin' => [
                'email' => env('NOTIFICATION_TEMPLATE_ORGANISATION_SIGNUP_RECEIVED_NOTIFY_GLOBAL_ADMIN_EMAIL', ''),
            ],
        ],
        'organisation_sign_up_form_approved' => [
            'notify_submitter' => [
                'email' => env('NOTIFICATION_TEMPLATE_ORGANISATION_SIGNUP_APPROVED_NOTIFY_SUBMITTER_EMAIL', ''),
            ],
        ],
        'organisation_sign_up_form_rejected' => [
            'notify_submitter' => [
                'email' => env('NOTIFICATION_TEMPLATE_ORGANISATION_SIGNUP_REJECTED_NOTIFY_SUBMITTER_EMAIL', ''),
            ],
        ],
    ],

];
