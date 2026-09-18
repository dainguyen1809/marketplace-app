<?php

return [
    'host'     => env('RABBITMQ_HOST', 'rabbitmq'),
    'port'     => (int) env('RABBITMQ_PORT', 5672),
    'user'     => env('RABBITMQ_USER', 'ecommerce'),
    'password' => env('RABBITMQ_PASSWORD', 'secret'),
    'vhost'    => env('RABBITMQ_VHOST', '/'),

    'connection_timeout' => (float) env('RABBITMQ_CONNECTION_TIMEOUT', 3.0),
    'read_write_timeout' => (float) env('RABBITMQ_READ_WRITE_TIMEOUT', 130.0),
    'heartbeat'          => (int) env('RABBITMQ_HEARTBEAT', 60),

    'dlx_exchange' => 'ecommerce.dlx',
    'dlx_queue'    => 'ecommerce.dead_letter_queue',
    'dlx_routing'  => 'dead_letter',
];

