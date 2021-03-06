<?php

namespace zaboy\test\async\Promise\Fulfilled;

use zaboy\async\Promise\Promise;
use zaboy\async\Promise\PromiseInterface;
use zaboy\res\Di\InsideConstruct;
use zaboy\test\async\Promise\DataProvider;
use zaboy\async\Promise\Exception\AlreadyRejectedException;
use zaboy\async\Promise\Exception\AlreadyFulfilledException;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-09-24 at 00:05:36.
 */
class PromiseTest extends DataProvider
{

    /**
     * @var Promise
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $container = include 'config/container.php';
        InsideConstruct::setContainer($container);
    }

    //====================== getState(); =======================================
    public function test_getState()
    {
        $promise = new Promise;
        $promise->resolve('foo');
        $this->assertEquals(PromiseInterface::FULFILLED, $promise->getState());
    }

    //====================== wait(); ===========================================
    public function test_wait_false()
    {
        $promise = new Promise;
        $promise->resolve('foo');
        $this->assertEquals('foo', $promise->wait(false));
    }

    public function test_wait_true()
    {
        $promise = new Promise;
        $promise->resolve('foo');
        $this->assertEquals('foo', $promise->wait(false));
    }

    //====================== resolve(); ========================================
    /**
     * @dataProvider provider_Types()
     */
    public function test_resolve_anyTypes_same_val_twice($in)
    {
        $promise = new Promise;
        $promise->resolve($in);
        $promise->resolve($in);
        $this->assertEquals($in, $promise->wait(false));
    }

    /**
     * @dataProvider provider_Types()
     */
    public function test_resolve_anyTypes_another_val($in)
    {
        $promise = new Promise;
        $promise->resolve('bar');
        $this->setExpectedExceptionRegExp(AlreadyFulfilledException::class, '|.*Cannot resolve a fulfilled promise|');
        $promise->resolve($in);
    }

    //====================== reject(); =========================================

    /**
     * @dataProvider provider_Types()
     */
    public function test_reject_anyTypes_another_val($in)
    {
        $promise = new Promise;
        $promise->resolve('bar');
        $this->setExpectedExceptionRegExp(AlreadyRejectedException::class, '|.*Cannot reject a fulfilled promise|');
        $promise->reject($in);
    }

    //====================== then(); ===========================================

    public function test_then()
    {
        $masterPromise = new Promise;
        $masterPromise->resolve('foo');
        $slavePromise = $masterPromise->then();
        $this->assertEquals(PromiseInterface::FULFILLED, $slavePromise->getState());
        $this->assertEquals('foo', $slavePromise->wait(false));
    }

    public function test_then_with_callbacks()
    {
        $onFulfilled = function($value) {
            return 'After $onFulfilled - ' . $value;
        };
        $onRejected = function($value) {
            return 'After $onRejected - ' . $value->getMessage();
        };
        $masterPromise = new Promise;
        $masterPromise->resolve('foo');
        $slavePromise = $masterPromise->then($onFulfilled, $onRejected);
        $this->assertEquals(PromiseInterface::FULFILLED, $slavePromise->getState());
        $this->assertEquals('After $onFulfilled - foo', $slavePromise->wait(false));
    }

}
