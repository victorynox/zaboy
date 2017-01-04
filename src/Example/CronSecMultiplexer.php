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
    public function __construct($interruptors)
    {
        parent::__construct($interruptors);
        $queue = new Queue('test_cron_sec_multiplexer');
        $extractor = new Extractor($queue);
        $this->interruptors[] = new Process(function () use ($extractor) {
            return $extractor->extract();
        });
    }

}
