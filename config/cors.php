<?php

return [
    /*
    | Cross-origin requests from the RideFlow Flutter web client.
    | The wildcard origin is suitable for development; use the deployed
    | frontend origin(s) in production.
    */
    // Include both normal Laravel URLs and installations served below /rideflow.
    'paths' => ['api/*', 'rideflow/api/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Authorization', 'Accept', 'Origin', 'X-Requested-With'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
