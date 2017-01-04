<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.01.17
 * Time: 13:24
 */

namespace zaboy\test\Queues;


use Interop\Container\ContainerInterface;
use zaboy\Callback\Example\CallMe;
use zaboy\Callback\Interruptor\Http;
use zaboy\Callback\Interruptor\Job;
use zaboy\Callback\Interruptor\Process;
use zaboy\Queues\Extractor;
use zaboy\Queues\Queue;


class ExtractorTest extends \PHPUnit_Framework_TestCase
{
    /** @var Extractor*/
    protected $object;

    /** @var Queue */
    protected $queue;

    protected $config;

    protected $queueName;

    public function setUp()
    {
        $this->queueName = 'test_extractor';
        $this->queue = new Queue($this->queueName);
        /** @var ContainerInterface $container */
        $container = include 'config/container.php';
        $this->config = $container->get('config');
    }

    public function provider_multiplexerType()
    {
        $stdObject = (object)['prop' => 'Hello '];
        //function
        return array(
            [
                [
                    function ($val) {
                        return 'Hello ' . $val;
                    },
                    function ($val) use ($stdObject) {
                        return $stdObject->prop . $val;
                    },
                    new CallMe(),
                    [new CallMe(), 'method'],
                    [new CallMe(), 'staticMethod'],
                    [CallMe::class, 'staticMethod'],
                    '\\' . CallMe::class . '::staticMethod'
                ],
                "World"
            ],
            [
                [
                    new Process(function ($val) {
                        return 'Hello ' . $val;
                    }),
                    new Process(function ($val) use ($stdObject) {
                        return $stdObject->prop . $val;
                    }),
                    new Process(new CallMe()),
                    new Process([new CallMe(), 'method']),
                    new Process([new CallMe(), 'staticMethod']),
                    new Process([CallMe::class, 'staticMethod']),
                    new Process('\\' . CallMe::class . '::staticMethod')
                ],
                "World"
            ],
            [
                [
                    function ($val) {
                        return 'Hello ' . $val;
                    },
                    new Process(function ($val) use ($stdObject) {
                        return $stdObject->prop . $val;
                    }),
                    new Process(new CallMe()),
                    new Http([new CallMe(), 'method'], $this->config['httpInterruptor']['url']),
                    new Process([new CallMe(), 'staticMethod']),
                    new Http([CallMe::class, 'staticMethod'], $this->config['httpInterruptor']['url']),
                    '\\' . CallMe::class . '::staticMethod'
                ],
                "World"
            ],
            [
                [
                    function ($val) {
                        throw new \Exception("some error");
                    },
                    new Process(function ($val) use ($stdObject) {
                        throw new \Exception("some error");
                    }),
                    new CallMe(),
                    [new CallMe(), 'method'],
                    '\\' . CallMe::class . '::staticMethod'
                ],
                "World"
            ],
        );
    }

    /**
     * @param $callbacks
     * @param $value
     * @dataProvider provider_multiplexerType()
     */
    public function test_extractQueue($callbacks, $value)
    {
        $this->object = new Extractor($this->queueName );
        foreach ($callbacks as $callback){
            $job = new Job($callback, $value);
            $this->queue->addMessage($job->serializeBase64());
        }
        $i = 0;
        while($this->object->extract()){
            $i++;
        };
        $this->assertEquals(count($callbacks), $i);
    }

    /**
     * @param $callbacks
     * @param $value
     * @dataProvider provider_multiplexerType()
     */
    public function test_extractQueueWithInterruptor($callbacks, $value)
    {
        $this->object = new Extractor($this->queueName, true);
        foreach ($callbacks as $callback){
            $job = new Job($callback, $value);
            $this->queue->addMessage($job->serializeBase64());
        }
        $i = 0;
        while($this->object->extract()){
            $i++;
        };
        $this->assertEquals(count($callbacks), $i);
    }
}
