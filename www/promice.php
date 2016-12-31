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

use zaboy\Callback\Callback;
//use zaboy\test\Callback\Interruptor\CallbackTestCallableProvider;
use zaboy\async\Promise\Promise;
use zaboy\Callback\Interruptor\Process;
use zaboy\res\Di\InsideConstruct;
use zaboy\Callback\Promiser;

InsideConstruct::setContainer($container);

$masterPromise = new Promise;

$promiser1 = new Promiser('strtoupper');
$slavePromise_1 = $masterPromise->then($promiser1);

$promiser2 = new Promiser('strtolower');
$slavePromise_2 = $masterPromise->then($promiser2);
//file_put_contents('666.txt', '$slavePromise_1' . $slavePromise_1->getId() . PHP_EOL, FILE_APPEND);

$masterPromise->resolve('qweRTY');

sleep(2);
var_dump($slavePromise_1->wait(false));
var_dump($slavePromise_2->wait(false));

//function prms($val)
//{
//    $callback = new Callback([$masterPromise, 'resolve']);
//    return $callback($val);
//}

//$slavePromise_1 = $masterPromise->then('strtoupper');
//$iPromise = new Promise;
//$callback = new Callback('strtolower');
//$i2Promise = $iPromise->then($callback);
//$interruptorProcess = new Process([$iPromise, 'resolve']);
//$slavePromise_2 = $masterPromise->then($interruptorProcess);