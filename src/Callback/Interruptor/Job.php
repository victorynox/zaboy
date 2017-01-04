<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\Callback\Interruptor;

use zaboy\Callback\CallbackException;

class Job
{

    protected $callback;
    protected $value;

    public function __construct(callable $callback, $value)
    {
        if (!is_callable($callback)) {
            throw new CallbackException('Callback is not callable');
        }
        if ($callback instanceof \Closure) {
            $callback = new SerializableClosure($callback);
        }
        $this->callback = $callback;
        $this->value = $value;
    }

    public function serializeBase64()
    {
        $serializedParams = serialize($this);
        $base64string = base64_encode($serializedParams);
        return $base64string;
    }

    public static function unserializeBase64($value)
    {
        return unserialize(base64_decode($value, true));
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getCallback()
    {
        return $this->callback;
    }

}
