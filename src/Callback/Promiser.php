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
use zaboy\Callback\Callback;
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

    /**
     *
     * @var InterruptorProcess
     */
    protected $interruptorProcess;

    /**
     *
     * @var Promise
     */
    protected $interruptorResalt;

    /**
     *
     * @var Promise
     */
    protected $promise;

    public function __construct(callable $callable)
    {
        parent::__construct($callable);
        $this->promise = new Promise; //$iPromise->then([$this, 'run']);
        $this->interruptorProcess = new InterruptorProcess([$this, 'runInProcess']);
    }

    public function __invoke($value)
    {

        if (isset($this->interruptorResalt) && is_array($this->interruptorResalt)) {
            throw new \LogicException('Do not call twise __invoke()');
        }
        $result = call_user_func($this->interruptorProcess, $value);
        if (isset($this->interruptorResalt)) {
            $this->interruptorResalt->resolve($result);
        } else {
            $this->interruptorResalt = $result;
        }
        return $this->promise;
    }

    public function runInProcess($value)
    {
        $result = $this->run($value);
        $this->promise->resolve($result);
        return $result;
    }

    public function __sleep()
    {
        if ($this->interruptorProcess instanceof \Closure) {
            $this->interruptorProcess = new SerializableClosure($this->interruptorProcess);
        }

        $array = parent::__sleep();
        $array[] = 'interruptorProcess';
        $array[] = 'interruptorResalt';
        $array[] = 'promise';

        return $array;
    }

    public function getInterruptorResalt()
    {
        if (!isset($this->interruptorResalt) || is_array($this->interruptorResalt)) {
            $promise = new Promise;
            if (is_array($this->interruptorResalt)) {
                $promise->resolve($this->interruptorResalt);
            }
            $this->interruptorResalt = $promise;
        } else {
            $promise = $this->interruptorResalt;
        }
        return $promise;
    }

}
