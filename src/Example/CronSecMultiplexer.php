<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.01.17
 * Time: 17:32
 */

namespace zaboy\Example;

use zaboy\Callback\Interruptor\Multiplexer;
use zaboy\Callback\Interruptor\Process;
use zaboy\Callback\Interruptor\Queue as QueueInterruptor;
use zaboy\Queues\Extractor;
use zaboy\Queues\Queue;

class CronSecMultiplexer extends Multiplexer
{
    const QUERY_NAME = 'test_cron_sec_multiplexer';

    public function __construct($interruptors)
    {
        parent::__construct($interruptors);
        $queue = new Queue(static::QUERY_NAME);
        $this->interruptors[] = new Process(function () use ($queue) {
            $extractor = new Extractor($queue);
            $extractor->extract();
        });
    }

}
