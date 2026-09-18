<?php

return [
    'private_key_path' => env('JWT_PRIVATE_KEY_PATH', 'infrastructure/Keys/private.pem'),
    'public_key_path'  => env('JWT_PUBLIC_KEY_PATH', 'infrastructure/Keys/public.pem'),
    'access_ttl'       => (int) env('JWT_ACCESS_TTL', 900),
    'refresh_ttl'      => (int) env('JWT_REFRESH_TTL', 604800),
    'issuer'           => env('JWT_ISSUER', 'ecommerce-api'),
];
