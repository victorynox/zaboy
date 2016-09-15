<?php

// Change to the project root, to simplify resolving paths
chdir(dirname(__DIR__));
require 'vendor/autoload.php';
$container = include 'config/container.php';

use zaboy\test\utils\Json\CoderTest;

$test = new CoderTest();

$test->run1();

use mindplay\jsonfreeze\JsonSerializer;
use zaboy\utils\Json\Serializer;
use zaboy\utils\Json\Exception as JsonException;

class A
{

    private $prop;

    public function setA($p)
    {
        $this->prop = $p;
    }

    public function getA()
    {
        return $this->prop;
    }

}

$a = new A();

$a->setA("a");

class B extends A
{

    private $prop;

    public function setB($p)
    {
        $this->prop = $p;
    }

    public function getB()
    {
        return $this->prop;
    }

}

$b = new B();


$s = new stdClass;
$s->a = 1;

//$b->setA($b);
//$b->setB($a);
$serializer = new JsonSerializer;
$str = $serializer->serialize($s);

$out = $serializer->unserialize('{"a": 1}');
var_dump($out);
//var_dump($b->getA());
//var_dump($b->getB());

exit;



$serializer = new Serializer();

$previous = new Exception('prop Exception1', 0);
$exc = new RuntimeException('prop RuntimeException', 0, $previous);

$valueExc = $serializer->jsonUnserialize($serializer->jsonSerialize($exc));
var_dump(null === $valueExc->getPrevious()); //true

$previous = new RuntimeException('prop RuntimeException', 0);
$exc = new Exception('prop Exception1', 0, $previous);

$valueExc = $serializer->jsonUnserialize($serializer->jsonSerialize($exc));
var_dump(null === $valueExc->getPrevious()); //false
exit;


$o = new stdClass();
$o->a = 1;
$o->b = 2;
var_dump(json_encode($o));
var_dump((array) json_decode(json_encode($o)));

exit;
//use zaboy\utils\Json\Serializer as JsonSerializer;
//use \RuntimeException;
//$previous = new Exception('prop Exception1', 0);
$exc = new RuntimeException('prop RuntimeException', 0, $previous);

$previous = new RuntimeException('prop RuntimeException', 0);
$exc = new Exception('prop Exception1', 0, $previous);
//
//class A
//{
//
//    private $prop;
//
//    public function set($p)
//    {
//        $this->prop = $p;
//    }
//
//    public function get()
//    {
//        return $this->prop;
//    }
//
//}
//
//$a = new A();
//
//$a->set(1);
//
//class B extends A
//{
//
//}
//
//$b = new B();
//$b->set(2);
//$serializer = new JsonSerializer();
//
//$value = $serializer->unserialize($serializer->serialize($a));
//var_dump($value->get()); //false
//
//$value = $serializer->unserialize($serializer->serialize($b));
//var_dump($value->get()); //true
//
//exit;
//$closure = function (A $aa) {
//    return $aa->prop;
//};
//// Closure::bind() на самом деле создает новое замыкание
//$closure = Closure::bind($closure, null, $a);
//var_dump($closure($b));
//
//exit;
//https://habrahabr.ru/post/186718/
//https://habrahabr.ru/post/138102/

$refClass = (new \ReflectionClass('B'))->getParentClass();
$refProperty = $refClass->getProperty('prop');
$refProperty->setAccessible(true);
var_dump($refProperty->getValue($b));
$refProperty->setValue($b, 3);
var_dump($refProperty->getValue($b));
$refProperty->setAccessible(false);
exit;

$str = JsonSerializer::jsonSerialize($previous);
$out = JsonSerializer::jsonUnserialize($str);
//var_dump($out); //false


$sweetsThief = new ReflectionProperty('Exception', 'trace');
$sweetsThief->setAccessible(true);
$properties = $class->getProperties();
var_dump($properties); //false

class foo
{

    private $bar = 42;

}

$obj = new foo;
$propname = "\0foo\0bar";
$a = (array) $obj;
echo $a[$propname];
