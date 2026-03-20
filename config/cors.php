<?php

return [
    // Cho phép các đường dẫn cần thiết
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'register'],

    'allowed_methods' => ['*'],

    // BẮT BUỘC LIỆT KÊ CHÍNH XÁC TÊN MIỀN TẠI ĐÂY
    'allowed_origins' => [
        'https://cameraclick.online',
        'https://www.cameraclick.online',
        'http://localhost:3000', // Nếu bạn dùng Next.js local
    ],

    'allowed_origins_patterns' => [
        '#^https://camera-click-fe-.*\.vercel\.app$#' // Cho link preview của Vercel
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Phải khớp với cấu hình Axios ở Frontend
];