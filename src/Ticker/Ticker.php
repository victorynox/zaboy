<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\Ticker;

use zaboy\res\Di\InsideConstruct;
use zaboy\scheduler\DataStore\UTCTime;

/**
 * Ticker
 *
 * @category   callback
 * @package    zaboy
 */
class Ticker
{

    /**
     *
     * @var $tickerCallback
     */
    protected $tickerCallback;

    /**
     *
     * @var int
     */
    protected $ticksCount;

    /**
     *
     * @var int in seconds
     */
    protected $tickDuration;

    public function __construct(callable $tickerCallback = null, $ticksCount = 60, $tickDuration = 1)
    {
        InsideConstruct::initServices();
    }

    public function secBySec60ticks()
    {
        for ($index = 0; $index < 60; $index++) {
            $startTime = UTCTime::getUTCTimestamp(5);
            $result[$startTime] = $this->everySec($index);
            $sleepTime = $startTime + 1 - UTCTime::getUTCTimestamp(5);
            usleep($sleepTime);
        }
        return $result;
    }

    public function everySec()
    {

        return $result;
    }

    public function everyMin()
    {

        return $result;
    }

    public function everyHour()
    {

        return $result;
    }

}
