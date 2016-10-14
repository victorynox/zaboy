<?php

//$path = getcwd();
//if (!is_file($path . '/vendor/autoload.php')) {
//    $path = dirname(getcwd());
//}
//chdir($path);
chdir(__DIR__ . '/../../../../');

require './vendor/autoload.php';

use zaboy\async\Callback\CallbackException;
use zaboy\Callback\Interruptor\Process;
use zaboy\Di\InsideConstruct;

/** @var Zend\ServiceManager\ServiceManager $container */
$container = include './config/container.php';
InsideConstruct::setContainer($container);

$paramsString = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : null;
$paramsArray = is_null($paramsString) ? null : unserialize(base64_decode($paramsString));

$callback = array_key_exists(Process::CALLBACK_KEY, $paramsArray) ?
        $paramsArray[Process::CALLBACK_KEY] : null;

$value = array_key_exists(Process::VALUE_KEY, $paramsArray) ?
        $paramsArray[Process::VALUE_KEY] : null;

try {
    if (!is_callable($callback)) {
        throw new CallbackException('Callback is not callable');
    }
    call_user_func($callback, $value);
    exit(0);
} catch (\Exception $e) {
    exit(1);
}

