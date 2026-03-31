<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    */

    'mailers' => [

        /*
        |--------------------------------------------------------------------------
        | FINAL FALLBACK (GMAIL)  ✅ KEEP
        |--------------------------------------------------------------------------
        */
        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', 'smtp.gmail.com'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env(
                'MAIL_EHLO_DOMAIN',
                parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)
            ),
        ],

        /*
        |--------------------------------------------------------------------------
        | ADMIN MAILER (UNTOUCHED ✅)
        |--------------------------------------------------------------------------
        */
        'gmail_admin' => [
            'transport' => 'smtp',
            'host' => env('MAIL_ADMIN_HOST'),
            'port' => env('MAIL_ADMIN_PORT'),
            'encryption' => env('MAIL_ADMIN_ENCRYPTION'),
            'username' => env('MAIL_ADMIN_USERNAME'),
            'password' => env('MAIL_ADMIN_PASSWORD'),
            'timeout' => 10,
            'from' => [
                'address' => env('MAIL_ADMIN_FROM_ADDRESS'),
                'name' => env('MAIL_ADMIN_FROM_NAME'),
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | STUDENT PRIMARY (BREVO ACCOUNT 1) ✅ FIXED
        |--------------------------------------------------------------------------
        */
        'gmail_student' => [
            'transport' => 'smtp',
            'host' => env('MAIL_STUDENT_HOST'),
            'port' => env('MAIL_STUDENT_PORT'),
            'encryption' => env('MAIL_STUDENT_ENCRYPTION'),
            'username' => env('MAIL_STUDENT_USERNAME'),
            'password' => env('MAIL_STUDENT_PASSWORD'),
            'timeout' => 10,
        ],

        /*
        |--------------------------------------------------------------------------
        | STUDENT FAILOVER (BREVO ACCOUNT 2) ✅ FIXED
        |--------------------------------------------------------------------------
        */
        'gmail_student_2' => [
            'transport' => 'smtp',
            'host' => env('MAIL_STUDENT_2_HOST'),
            'port' => env('MAIL_STUDENT_2_PORT'),
            'encryption' => env('MAIL_STUDENT_2_ENCRYPTION'),
            'username' => env('MAIL_STUDENT_2_USERNAME'),
            'password' => env('MAIL_STUDENT_2_PASSWORD'),
            'timeout' => 10,
        ],

        /*
        |--------------------------------------------------------------------------
        | STUDENT FAILOVER CHAIN ⭐
        |--------------------------------------------------------------------------
        */
        'student_failover' => [
            'transport' => 'failover',
            'mailers' => [
                'gmail_student',     // Brevo #1
                'gmail_student_2',   // Brevo #2
                'smtp',              // Gmail final fallback
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | OTHER DEFAULT MAILERS (UNCHANGED)
        |--------------------------------------------------------------------------
        */

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => ['smtp', 'log'],
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => ['ses', 'postmark'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global From Address
    |--------------------------------------------------------------------------
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS'),
        'name' => env('MAIL_FROM_NAME'),
    ],

];