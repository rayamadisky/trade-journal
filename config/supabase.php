<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supabase Project Configuration
    |--------------------------------------------------------------------------
    |
    | These values are used to connect to your Supabase project for
    | authentication, storage, and REST API operations.
    |
    */

    'url' => env('SUPABASE_URL', ''),

    'anon_key' => env('SUPABASE_ANON_KEY', ''),

    'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY', ''),

    'storage_bucket' => env('SUPABASE_STORAGE_BUCKET', 'trade-screenshots'),

];
