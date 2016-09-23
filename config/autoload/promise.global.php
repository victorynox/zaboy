<?php

return [
    'services' => [
        'aliases' => [
            //this 'callback' is service name in url
            'promiseDbAdapter' => getenv('APP_ENV') === 'production' ? 'db' : 'testDb',
        ],
    ],
];
