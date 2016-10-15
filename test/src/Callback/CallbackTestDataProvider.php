<?php

namespace zaboy\test\Callback;

require_once './src/Callback/Example/CallMe.php';

use zaboy\Callback\Callback;
use zaboy\Callback\Example\CallMe;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-09-20 at 12:54:48.
 */
class CallbackTestDataProvider extends \PHPUnit_Framework_TestCase
{

    public function provider_mainType()
    {
        $stdObject = (object) ['prop' => 'Hello '];

        //function
        return array(
            [
                'class_parents',
                self::class,
                [
                    'PHPUnit_Framework_TestCase' => "PHPUnit_Framework_TestCase",
                    'PHPUnit_Framework_Assert' => "PHPUnit_Framework_Assert"
                ]
            ],
            //closure
            [
                function ($val) {
                    return 'Hello ' . $val;
                },
                'World',
                'Hello World'
            ],
            //closure with uses
            [
                function ($val) use ($stdObject) {
                    return $stdObject->prop . $val;
                },
                'World',
                'Hello World'
            ],
            //invokable object
            [
                new CallMe(),
                'World',
                'Hello World'
            ],
            //method
            [
                [ new CallMe(), 'method'],
                'World',
                'Hello World'
            ],
            //static method
            [
                [ new CallMe(), 'staticMethod'],
                'World',
                'Hello World'
            ],
            [
                [CallMe::class, 'staticMethod'],
                'World',
                'Hello World'
            ],
            [
                '\\' . CallMe::class . '::staticMethod',
                'World',
                'Hello World'
            ],
        );
    }

}
