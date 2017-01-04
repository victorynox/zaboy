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
use zaboy\Queues\QueueInterface;

class Queue extends InterruptorAbstract implements InterruptorInterface
{
    /** @var  QueueInterface */
    protected $queue;

    public function __construct(callable $callback, QueueInterface $queue)
    {
        parent::__construct($callback);
        $this->queue = $queue;
    }

    /**
     * @param \zaboy\Callback\mix $value
     * @return mixed
     */
    public function __invoke($value)
    {
        $callback = $this->getCallback();

        $job = new Job($callback, $value);
        $this->queue->addMessage($job->serializeBase64());

        $result[static::MACHINE_NAME_KEY] = getenv(static::ENV_VAR_MACHINE_NAME);

        $result[static::INTERRUPTOR_TYPE_KEY] = static::class;
        return $result;
    }
}
