<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'register'],
'allowed_methods' => ['*'],
'allowed_origins' => ['http://localhost:3000','http://127.0.0.1:3000'], // Địa chỉ Next.js của bạn
'allowed_origins_patterns' => [],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => true,
];
