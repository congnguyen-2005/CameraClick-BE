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
    */

    // Cho phép tất cả các đường dẫn trong api
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'register', 'logout'],

    // Cho phép tất cả các phương thức (GET, POST, PUT, DELETE,...)
    'allowed_methods' => ['*'],

    // MỞ CỬA HOÀN TOÀN: Cho phép tất cả các domain (Vercel link nào cũng chạy được)
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    /* 
    | QUAN TRỌNG: Khi để allowed_origins là ['*'], 
    | bạn PHẢI để supports_credentials là false.
    | Vì bạn dùng Bearer Token trong Header nên để false vẫn chạy cực tốt.
    */
    'supports_credentials' => false,
];