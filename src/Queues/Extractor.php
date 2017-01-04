<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.01.17
 * Time: 11:56
 */

namespace zaboy\Queues;

use Xiag\Rql\Parser\Query;
use zaboy\Callback\Interruptor\Job;
use zaboy\Callback\Interruptor\Process;
use zaboy\Callback\InterruptorInterface;
use zaboy\Callback\Promiser;
use zaboy\Callback\PromiserInterface;

class Extractor
{

    /** @var Queue */
    protected $queue;

    /** @var bool  */
    protected $forceInterruptor;

    /** @var string */
    protected $interruptorType;

    public function __construct($queueName, $forceInterruptor = false, $interruptorType = Process::class)
    {
        $this->queue = new Queue($queueName);
        $this->forceInterruptor = $forceInterruptor;
        $this->interruptorType = $interruptorType;
    }

    public function extract()
    {
        $message = $this->queue->getMessage();
        if (isset($message)){
            $job = Job::unserializeBase64($message->getData());
            $callback = $this->warpInInterruptor($job->getCallback());
            $value = $job->getValue();
            try{
                call_user_func($callback, $value);
            } catch (\Exception $e) {}
            return true;
        }
        return false;
    }

    protected function warpInInterruptor(callable $callback)
    {
        return ($this->forceInterruptor
            && !$callback instanceof InterruptorInterface
            && !$callback instanceof PromiserInterface)
            ? new $this->interruptorType($callback) : $callback;
    }
}
