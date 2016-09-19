<?php

return [

    'db' => [
        'adapters' => [
            'db' => [
                'driver' => 'Pdo_Mysql',
                'database' => 'zaboy',
                'username' => 'uzaboy_rest',
                'password' => '123321qweewq'
            ]
        ]
    ],
    'services' => [
        'abstract_factories' => [
            'Zend\Db\Adapter\AdapterAbstractServiceFactory',
        ]
    ]
];
