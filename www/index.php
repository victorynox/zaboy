<?php

// Change to the project root, to simplify resolving paths
chdir(dirname(__DIR__));
require 'vendor/autoload.php';
$container = include 'config/container.php';

use Opis\Closure\SerializableClosure;
use zaboy\Di\InsideConstruct;
use Zend\Db\Adapter\AdapterInterface;

class A
{

    public function __construct(ArrayObject $o)
    {
        InsideConstruct::initServices();
        return;
    }

}

class B
{

    protected $db;

    public function __construct(stdClass $db = null)
    {


        InsideConstruct::initServices();
        return;
    }

}

new B(null);
var_dump(new B);
exit;
// Recursive factorial closure
$factorial = function ($n) use (&$factorial) {
    return $n <= 1 ? 1 : $factorial($n - 1) * $n;
};

// Wrap the closure
$wrapper = new SerializableClosure($factorial);
// Now it can be serialized
$serialized = serialize($wrapper);

var_dump($wrapper);
