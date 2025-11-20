<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'recaptcha' => [
        'site_key_v2' => env('RECAPTCHA_SITE_KEY_V2'),
        'secret_key_v2' => env('RECAPTCHA_SECRET_KEY_V2'),
        'site_key_v3' => env('RECAPTCHA_SITE_KEY_V3', '6Lc01OorAAAAAHLhyGqzMuFb3UIVQRwA5U3zYCT7'),
        'secret_key_v3' => env('RECAPTCHA_SECRET_KEY_V3', '6Lc01OorAAAAAAUacjcwDrItgV5BiZw51hytdG74'),
    ],

    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'mapbox' => [
        'access_token' => env('MAPBOX_ACCESS_TOKEN'),
    ],

];
