<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Encryption Keys
    |--------------------------------------------------------------------------
    */

    'private_key' => env('POLICY_PRIVATE_KEY'),

    'public_key' => env('POLICY_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Policy Storage Driver
    |--------------------------------------------------------------------------
    |
    | This configuration value allows you to customize the storage options
    | for Policy, such as the database connection that should be used
    | by Policy's internal database models which store tokens, etc.
    |
    */

    'storage' => [
        'database' => [
            'connection' => env('DB_CONNECTION', 'mysql')
        ],
    ],

    'route' => [
        'web' => ['web', 'auth:web'],
        'api' => ['api', 'auth:api']
    ],

];
