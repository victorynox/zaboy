<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.01.17
 * Time: 14:57
 */

namespace zaboy\Callback\Interruptor;

use zaboy\Callback\Callback;
use zaboy\Callback\InterruptorInterface;
use zaboy\Queues\Queue;

class QueueInterrupter extends Callback implements InterruptorInterface
{
    /** @var  Queue */
    protected $queue;

    public function __construct(callable $callback, Queue $queue)
    {
        parent::__construct($callback);
        $this->queue;
    }

    public function __invoke($value) {
        $callback = $this->getCallback();
        $job = new Job($callback, $value);
        $this->queue->addMessage($job->serializeBase64());

        $result[strtolower(Process::SERVICE_MACHINE_NAME_KEY)] = getenv(Process::SERVICE_MACHINE_NAME_KEY);
        $result[Process::INTERRUPTOR_TYPE_KEY] = static::class;
        return $result;
    }
}
