<?php

//$path = getcwd();
//if (!is_file($path . '/vendor/autoload.php')) {
//    $path = dirname(getcwd());
//}
//chdir($path);
chdir(__DIR__ . '/../../../../');

require './vendor/autoload.php';

use zaboy\async\Callback\CallbackException;
use zaboy\res\Di\InsideConstruct;
use zaboy\Callback\Interruptor\Job;

/** @var Zend\ServiceManager\ServiceManager $container */
$container = include './config/container.php';
InsideConstruct::setContainer($container);

$paramsString = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : null;

try {
    if (is_null($paramsString)) {
        throw new CallbackException('There is not params string');
    }
    /* @var $job Job */
    $job = Job::unserializeBase64($paramsString);
    $callback = $job->getCallback();
    $value = $job->getValue();
    file_put_contents('logog.txt', $value);
    call_user_func($callback, $value);
    exit(0);
} catch (\Exception $e) {
    exit(1);
}

