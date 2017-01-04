<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.01.17
 * Time: 17:47
 */

namespace zaboy\Ticker\Example;

use zaboy\Example\CronMinMultiplexer;
use zaboy\Example\CronSecMultiplexer;
use zaboy\Ticker\Ticker;
use zaboy\utils\UtcTime;

class TickerCron extends Ticker
{
    public function everySec()
    {
        $cronSecMultiplexor =  new CronSecMultiplexer([]);
        $cronSecMultiplexor('');
        return UtcTime::getUtcTimestamp(5);
    }

    public function everyMin()
    {
        $cronMinMultiplexor =  new CronMinMultiplexer([]);
        return $cronMinMultiplexor('');
    }

}
