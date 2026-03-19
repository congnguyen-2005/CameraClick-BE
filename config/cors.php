<?php

return [
    // Cho phép tất cả các đường dẫn bắt đầu bằng api/
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'register'],

    'allowed_methods' => ['*'],

    // SỬA DÒNG NÀY: Thay localhost bằng dấu '*' 
    // Dấu '*' nghĩa là cho phép mọi nguồn (bao gồm cả Vercel) truy cập
'allowed_origins' => [
    'https://camera-click-fe-3q2k-onrpjoc81-congnguyen-2005s-projects.vercel.app'
],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];