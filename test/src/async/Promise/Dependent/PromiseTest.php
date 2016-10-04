<?php

namespace zaboy\test\async\Promise\Dependent;

use zaboy\async\Promise\Promise;
use zaboy\async\Promise\PromiseInterface;
use zaboy\async\Promise\Exception\TimeIsOutException;
use zaboy\Di\InsideConstruct;
use zaboy\test\async\Promise\DataProvider;
use zaboy\async\Promise\Exception\AlreadyResolvedException;

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
        $masterPromise = new Promise;
        $slavePromise = $masterPromise->then();
        $this->assertEquals(PromiseInterface::PENDING, $slavePromise->getState());
    }

    //====================== wait(); ===========================================
    public function test_wait_false()
    {
        $masterPromise = new Promise;
        $slavePromise = $masterPromise->then();
        $this->assertContainsOnlyInstancesOf(TimeIsOutException::class, [$slavePromise->wait(false)]);
    }

    public function test_wait_true()
    {
        $masterPromise = new Promise;
        $slavePromise = $masterPromise->then();
        $this->setExpectedException(TimeIsOutException::class);
        $slavePromise->wait(); //it is equal ->wait(true)
    }

    //====================== resolve(); ========================================

    public function test_resolve_slavePromise_by_masterPromise()
    {
        $masterPromise = new Promise;
        $slavePromise = $masterPromise->then();
        $slavePromise->resolve($masterPromise);
    }

    /**
     * @dataProvider provider_Types()
     */
    public function test_resolve_slavePromise_by_anyTypes($in)
    {
        $masterPromise = new Promise;
        $slavePromise = $masterPromise->then();
        $this->setExpectedExceptionRegExp(
                AlreadyResolvedException::class
                , '|.*You can resolve dependent promise only by its master promise|'
        );
        $slavePromise->resolve($in);
    }

    /**
     * @dataProvider provider_Types()
     */
    public function test_resolve_masterPromise_by_anyTypes($in)
    {
        $masterPromise = new Promise;
        $slavePromise = $masterPromise->then();
        $masterPromise->resolve($in);
        $this->assertEquals(PromiseInterface::FULFILLED, $slavePromise->getState());
        $this->assertEquals($in, $slavePromise->wait(false));
    }

    //====================== reject(); =========================================

    public function test_reject_slavePromise_by_TimeIsOutException()
    {
        $masterPromise = new Promise;
        $slavePromise = $masterPromise->then();
        $slavePromise->reject(new TimeIsOutException('foo'));
        $this->assertEquals(PromiseInterface::REJECTED, $slavePromise->getState());
    }

    public function test_reject_slavePromise_by_masterPromise()
    {
        $masterPromise = new Promise;
        $slavePromise = $masterPromise->then();
        $this->setExpectedExceptionRegExp(
                AlreadyResolvedException::class
                , '|.*You can resolve dependent promise only by its master promise|'
        );
        $slavePromise->reject($masterPromise);
    }

    /**
     * @dataProvider provider_Types()
     */
    public function test_reject_slavePromise_by_anyTypes($in)
    {
        $masterPromise = new Promise;
        $slavePromise = $masterPromise->then();
        $this->setExpectedExceptionRegExp(
                AlreadyResolvedException::class
                , '|.*You can resolve dependent promise only by its master promise|'
        );
        $slavePromise->reject($in);
    }

    //====================== then(); ===========================================
}