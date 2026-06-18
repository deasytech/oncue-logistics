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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'paystack' => [
        'secret' => env('PAYSTACK_SECRET_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'places_api_key' => env('GOOGLE_PLACES_API_KEY'),
    ],

    'twilio' => [
        'sid' => env('TWILIO_ACCOUNT_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        'from' => env('TWILIO_PHONE_NUMBER'),
        'whatsapp_from' => env('TWILIO_WHATSAPP_NUMBER'),
        'messaging_service_sid' => env('TWILIO_MSG_SERVICE_SID'),
        'sender_id' => env('TWILIO_SENDER_ID'),
        'default_country_code' => env('TWILIO_DEFAULT_COUNTRY_CODE', '234'),
        'rsvp_template_sid' => env('TWILIO_RSVP_TEMPLATE_SID'),
    ],

    'vonage' => [
        'key' => env('VONAGE_API_KEY'),
        'secret' => env('VONAGE_API_SECRET'),
        'from' => env('VONAGE_SMS_FROM'),
        'whatsapp_from' => env('VONAGE_WHATSAPP_FROM'),
    ],

    'delivery' => [
        'middleware_url' => env('DELIVERY_MIDDLEWARE_URL', 'http://oncue-delivery-middleware.test'),
    ],

];
