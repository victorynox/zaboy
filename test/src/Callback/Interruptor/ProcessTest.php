<?php

namespace zaboy\test\Interruptor\Callback;

use zaboy\Callback\Callback;
use zaboy\async\Promise\Promise;
use zaboy\Callback\Interruptor\Process;
use zaboy\Di\InsideConstruct;
use zaboy\Callback\Promiser;
use zaboy\test\Callback\CallbackTestDataProvider;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-10-13 at 12:52:54.
 */
class ProcessTest extends CallbackTestDataProvider
{

    protected function setUp()
    {
        $container = include 'config/container.php';
        InsideConstruct::setContainer($container);
    }

    /**
     * @covers zaboy\Callback\Callback::__wakeup
     * @dataProvider provider_mainType()
     */
    public function test__wakeupWithPromise($callable, $val, $expected)
    {
        $callback = new Callback($callable);
        $wakeupedCallback = unserialize(serialize($callback));
        $promiser = new Promiser($wakeupedCallback);
        $interruptorResaltPromise = $promiser->getInterruptorResalt();

        $masterPromise = new Promise();
        $slavePromise = $masterPromise->then($promiser);
        $masterPromise->resolve($val);

        $this->assertEquals($expected, $slavePromise->wait(true));

        $interruptorResalt = $interruptorResaltPromise->wait();
        $this->assertFileExists($interruptorResalt[Process::STDOUT_KEY]);
        $this->assertFileExists($interruptorResalt[Process::STDERR_KEY]);
        if (substr(php_uname(), 0, 7) === "Windows") {
            $this->assertEquals('', $interruptorResalt[Process::PID_KEY]);
        } else {
            $this->assertNotSame('', $interruptorResalt[Process::PID_KEY]);
        }
    }

    public function test__wakeupWithPromiseParallels()
    {

        $callback = new Callback(function ( $val) {
            sleep(1);
            return microtime(1);
        });

        $masterPromise = new Promise();
        $slavePromise_1 = $masterPromise->then(new Promiser($callback));
        $slavePromise_2 = $masterPromise->then(new Promiser($callback));
        $masterPromise->resolve(1); //in sec

        if (abs($slavePromise_1->wait() - $slavePromise_2->wait()) < 0.5) {
            $result = 'parallel';
        } else {
            $result = 'in series';
        }

        var_dump(abs($slavePromise_1->wait() - $slavePromise_2->wait()));

        if (substr(php_uname(), 0, 7) === "Windows") {
            $this->assertEquals('in series', $result);
        } else {
            $this->assertNotSame('parallel', $result);
        }
    }

}
