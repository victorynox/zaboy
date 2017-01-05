<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.01.17
 * Time: 17:47
 */

namespace zaboy\Ticker\Example;

use zaboy\Callback\Interruptor\Process;
use zaboy\Example\CronMinMultiplexer;
use zaboy\Example\CronSecMultiplexer;
use zaboy\Ticker\Ticker;
use zaboy\utils\UtcTime;

class TickerCron extends Ticker
{
    public function __construct(callable $tickerCallback = null, $ticksCount = 3, $tickDuration = 1)
    {
        parent::__construct($tickerCallback, $ticksCount, $tickDuration);
    }

    public function everySec()
    {
        $cronSecMultiplexor =  new CronSecMultiplexer([]);
        $cronSecMultiplexor('');
        return UtcTime::getUtcTimestamp(5);
    }

    public function everyMin()
    {
        $cronMinMultiplexor =  new CronMinMultiplexer([
            new Process(function () {
                $this->secBySec60ticks();
            })
        ]);
        return $cronMinMultiplexor('');
    }

}
