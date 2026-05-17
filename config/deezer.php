<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | API Endpoint
    |--------------------------------------------------------------------------
    |
    | Deezer's public Search and Get endpoints do not require authentication.
    | OAuth is only required for endpoints that act on a specific user (e.g.
    | favourites, history), which this wrapper does not currently cover.
    |
    */

    'endpoints' => [
        'api' => env('DEEZER_API_URL', 'https://api.deezer.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Default values applied to every request unless overridden on the
    | individual action via the fluent builder (e.g. ->limit(50)).
    |
    */

    'defaults' => [
        'limit' => 25,
        'index' => 0,
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client
    |--------------------------------------------------------------------------
    */

    'http' => [
        'timeout' => env('DEEZER_HTTP_TIMEOUT', 10),
        'retry' => [
            'times' => env('DEEZER_HTTP_RETRY_TIMES', 1),
            'sleep' => env('DEEZER_HTTP_RETRY_SLEEP', 200),
        ],
    ],

];
