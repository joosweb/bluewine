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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => '195909152191-lens1pelhittdali9dcpk3lr42iko6bt.apps.googleusercontent.com',
        'client_secret' => 'TFaYplVoNFkbrAWZb_kDoLaW',
        'redirect' => 'http://localhost:8000/auth/google/callback',
    ],

    'facebook' => [
    'client_id' => '2602263759985234',
    'client_secret' => '720536ee541e3384793505c37fabfc00',
    'redirect' => 'http://localhost:8000/auth/facebook/callback'
    ],

    'github' => [
      'client_id' => 'c0b2293a3a276dfd79e5',
      'client_secret' => '3719747f0afbad5c3c0f6c12bc53bc1c7999bd72',
      'redirect' => 'http://localhost:8000/auth/github/callback',
    ]
];
