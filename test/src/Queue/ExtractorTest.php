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
        $queueName = 'test_extractor';
        $this->queue = new Queue($queueName);
        /** @var ContainerInterface $container */
        $container = include 'config/container.php';
       // $this->config = $container->get('config');
    }

    public function provider_type()
    {
        $stdObject = (object)['prop' => 'Hello '];
        //function
        return array(
            [
                [
                    function ($val) {
                        return 'Hello ' . $val;
                    },
                    new Process(function ($val) use ($stdObject) {
                        return $stdObject->prop . $val;
                    }),
                    new Process(new CallMe()),
                    new Process([new CallMe(), 'staticMethod']),
                    '\\' . CallMe::class . '::staticMethod'
                ],
                "World"
            ],
        );
    }

    /**
     * @param $callbacks
     * @param $value
     * @dataProvider provider_type()
     */
    public function test_extractQueue($callbacks, $value)
    {
        $this->object = new Extractor($this->queue);
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
