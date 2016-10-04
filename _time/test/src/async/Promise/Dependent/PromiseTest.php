<?php

namespace zaboy\test\async\Promise\Dependent;

use zaboy\async\Promise\Promise;
use zaboy\async\Promise\PromiseInterface;
use zaboy\async\Promise\Promise\Pending as PendingPromise;
use zaboy\async\Promise\TimeIsOutException;
use zaboy\async\Promise\RejectedException;
use zaboy\Di\InsideConstruct;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-09-24 at 00:05:36.
 */
class PromiseTest extends \PHPUnit_Framework_TestCase
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
        $slavePromise->wait();
    }

    //====================== resolve(); ========================================
    /**
     * @dataProvider provider_Types()
     */
    public function test_resolve_anyTypes($in)
    {
        $masterPromise = new Promise;
        $slavePromise = $masterPromise->then();
        $masterPromise->resolve($in);
        $this->assertEquals($in, $slavePromise->wait(false));
    }

    public function provider_Types()
    {
        return [
            array(false),
            array(-12345),
            array('foo'),
            array([1, 'foo', [], false]),
            array(new \stdClass()),
            array(new \LogicException('bar')),
        ];
    }

    //====================== reject(); =========================================
    //====================== then(); ===========================================
}