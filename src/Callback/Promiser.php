<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\Callback;

use zaboy\Callback\CallbackException;
use Opis\Closure\SerializableClosure;
use zaboy\async\Promise\Promise;
use zaboy\async\Callback\Interrupter\InterrupterInterface;
use zaboy\Callback\Interruptor\Process as InterruptorProcess;

/**
 * Callback
 *
 * @category   callback
 * @package    zaboy
 */
class Promiser extends Callback
{

    public function __invoke($value)
    {
        $iPromise = new Promise;
        $i2Promise = $iPromise->then($this);
        $interruptorProcess = new InterruptorProcess([$iPromise, 'resolve']);
        return $i2Promise;
    }

}
