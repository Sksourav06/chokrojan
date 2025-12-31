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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // 'sms' => [
    //     'default' => env('SMS_DEFAULT_PROVIDER', 'generic'),

    //     'generic' => [
    //         'url' => env('GENERIC_SMS_API_URL'),
    //         // username এবং password ব্যবহার করে API Key নেওয়া হচ্ছে
    //         'username' => env('GENERIC_USERNAME'),
    //         'password' => env('GENERIC_PASSWORD'),
    //         'source_id' => env('GENERIC_SOURCE_ID'), // এটিও .env তে সংজ্ঞায়িত হতে হবে
    //     ],
    // ],

    'sms' => [
        'default' => env('SMS_DEFAULT_PROVIDER', 'generic'),

        'generic' => [
            'url' => env('SMS_NET_BD_API_URL'),
            // API Key টিকেই username হিসেবে পাস করা হচ্ছে
            'username' => env('SMS_NET_BD_API_KEY'),
            // যেহেতু আপনার কাছে পাসওয়ার্ড বা সোর্স আইডি নেই, তাই এগুলো .env থেকে null/খালি আসবে
            'password' => env('GENERIC_PASSWORD'),
            'source_id' => env('GENERIC_SOURCE_ID'),
        ],
    ],

];
