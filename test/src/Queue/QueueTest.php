<?php

namespace zaboy\test\Queues;

use zaboy\Queues\Queue;
use zaboy\async\Promise\Promise;
use zaboy\res\Di\InsideConstruct;

class QueueTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Queue
     */
    protected $object;

    protected function setUp()
    {
//        $container = include 'config/container.php';
//        InsideConstruct::setContainer($container);

        $this->object = new Queue('test_queue');
        $this->object->purgeQueue('test_queue');
    }

    public function test__getNullMessage()
    {
        $message = $this->object->getMessage();
        $this->assertEquals(null, $message);
    }

    public function test__addMessage()
    {

        $this->object->addMessage('test1');
        $message = $this->object->getMessage();
        $this->assertEquals('test1', $message->getData());
    }

}
