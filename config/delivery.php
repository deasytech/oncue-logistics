<?php

return [
  /*
    |--------------------------------------------------------------------------
    | Delivery Middleware Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the external delivery middleware
    | service integration.
    |
    */

  'middleware_url' => env('DELIVERY_MIDDLEWARE_URL', 'https://oncue-delivery-middleware.test'),
  'middleware_api_key' => env('DELIVERY_MIDDLEWARE_API_KEY', ''),

  'endpoints' => [
    'orders' => '/api/orders',
    'zones' => '/api/zones',
  ],

  'timeout' => 30,
];
