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

/**
 * Callback
 *
 * @category   callback
 * @package    zaboy
 */
class Callback
{

    /**
     *
     * @var Callable
     */
    protected $callback;

    public function __construct(callable $callback)
    {
        $this->setCallback($callback);
    }

    /**
     *
     * @param mix $value
     * @param Promise|true|null $promise
     * @return Promise|mix if Interrupter is retrived in __construct - Promise returned
     * @throws CallbackException
     */
    public function __invoke($value, $promise = null)
    {
        if (is_callable($this->getCallback(), true)) {
            try {
                return call_user_func($this->getCallback(), $value);
            } catch (\Exception $exc) {
                throw new CallbackException(
                'Cannot execute Callback. Reason: ' . $exc->getMessage(), 0, $exc
                );
            }
        } else {
            throw new CallbackException(
            'There was not correct instance callable in Callback'
            );
        }
    }

    public function __sleep()
    {
        $callback = $this->getCallback();
        if ($callback instanceof \Closure) {
            $callback = new SerializableClosure($callback);
            $this->setCallback($callback);
        }
        return array('callback');
    }

    public function __wakeup()
    {
        $callback = $this->getCallback();
        if (!is_callable($callback, true)) {
            throw new CallbackException(
            'There is not correct instance callable in Callback'
            );
        }
    }

    protected function getCallback()
    {
        return $this->callback;
    }

    protected function setCallback(callable $callback)
    {
        $this->callback = $callback;
    }

}
