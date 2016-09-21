<?php

return [
    'services' => [
        'aliases' => [
            //this 'callback' is service name in url
            'entityDbAdapter' => getenv('APP_ENV') === 'production' ? 'db' : 'testDb',
        ],
    ],
];
