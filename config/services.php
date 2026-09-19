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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    /*
    |--------------------------------------------------------------------------
    | Bounce Email IMAP Configuration (OVH)
    |--------------------------------------------------------------------------
    |
    | Boîte email dédiée à la réception des NDR (bounces).
    | Configurer dans .env :
    |   BOUNCE_EMAIL=bounce@caei-afri.com
    |   BOUNCE_IMAP_HOST=ssl://imap.mail.ovh.net
    |   BOUNCE_IMAP_PORT=993
    |   BOUNCE_IMAP_USER=bounce@caei-afri.com
    |   BOUNCE_IMAP_PASS=votre-mot-de-passe
    |
    */
    'bounce_imap' => [
        'address'  => env('BOUNCE_EMAIL'),
        'host'     => env('BOUNCE_IMAP_HOST', 'ssl://imap.mail.ovh.net'),
        'port'     => (int) env('BOUNCE_IMAP_PORT', 993),
        'username' => env('BOUNCE_IMAP_USER'),
        'password' => env('BOUNCE_IMAP_PASS'),
    ],

];
