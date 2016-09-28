<?php

//я придерживаюсь следующих критериев при разделения конфигов:
//1. Привязка конфига к имени сервера
//2. По умолчанию использовать продакшн конфиг
//3. Должна быть возможность определить несколько доменов (таже с помошью регулярок) для одного конфига
//4. Должна быть возможность выбрать конфиг с помошью параметра в запросе (только для разрешенных айпи)
//        use Zend\Stratigility\FinalHandler
//
//        $final = new FinalHandler([
//            'env' => 'production',
//        ]);
//        //putenv("APP_ENV=pro"); - it is for compose autoload config cache
//        putenv("APP_ENV=dev");
//        //
//        // - it is for MiddlewarePipe debug information
//        $env = getenv('APP_ENV') === 'dev' ? 'develop' : null;
//        //
//        //'env' => 'develop' (error ifor show)) or 'env' => 'any another' (do not show))
//        $app = new MiddlewarePipeOptions(['env' => $env]);
//
//        ================================================================================
// Set error reporting
// Define application environment

if (getenv('APP_ENV') === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Change to the project root, to simplify resolving paths
chdir(dirname(__DIR__));
require 'vendor/autoload.php';
$container = include 'config/container.php';

use zaboy\async\Promise\Promise;
use zaboy\async\Promise\PromiseInterface;

//$this->setExpectedException('\LogicException');
$slavePromise = new Promise;
$masterPromise = new Promise;
$slavePromise->resolve($masterPromise);
$masterPromise->resolve('foo');




