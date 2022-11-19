<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
     */

    'paths' => ['api/*','api/getData/*', 'api/getDataWithText/*','*'],

    'allowed_methods' => ['GET','POST','*'],

    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000','https://seo-frontend.netlify.app')],

    'allowed_origins_patterns' => ['Google/'],

    'allowed_headers' => ['X-Custom-Header', 'Upgrade-Insecure-Requests', '*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];