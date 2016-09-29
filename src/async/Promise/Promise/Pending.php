<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\async\Promise\Promise;

use zaboy\async\Promise\Promise;
use zaboy\async\Promise\Store as PromiseStore;
use zaboy\async\Promise\Promise\Fulfilled as FulfilledPromise;
use zaboy\async\Promise\Promise\Rejected as RejectedPromise;
use zaboy\async\Promise\Promise\Dependent as DependentPromise;
use zaboy\async\Entity\Entity;
use zaboy\async\Promise\PromiseInterface;

/**
 * Pending Promise
 *
 * @category   async
 * @package    zaboy
 */
class Pending extends Entity implements PromiseInterface
{

    /**
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
//$id = isset($data[PromiseStore::ID]) ? $data[PromiseStore::ID] : null;
        parent::__construct($data);
        $this[PromiseStore::STATE] = PromiseInterface::PENDING;
        $this[PromiseStore::RESULT] = null;
        $this[PromiseStore::ON_FULFILLED] = null;
        $this[PromiseStore::ON_REJECTED] = null;
        $this[PromiseStore::PARENT_ID] = null;
    }

    public function resolve($value)
    {
//If promise and x refer to the same object, reject promise with a TypeError as the reason.
        if ($value == $this) {
            $exc = new \UnexpectedValueException('TypeError. ID = ' . $this->getId());
            $this[PromiseStore::RESULT] = $exc;
            return new RejectedPromise($this->getData());
        }

//If then is not a function, fulfill promise with x.
        if (!is_object($value) || !$value instanceof PromiseInterface) {
            $this[PromiseStore::RESULT] = $value;
            return new FulfilledPromise($this->getData());
        }

//If x is pending, promise must remain pending until x is fulfilled or rejected.
//If/when x is fulfilled, fulfill promise with the same value.
//If/when x is rejected, reject promise with the same reason
        $state = $value->getState();
        if ($state === PromiseInterface::PENDING) {
            $lockedPromise = new Promise($value->getId());
            $state = $lockedPromise->getState();
        }
        switch ($state) {
            case PromiseInterface::PENDING:
                $this[PromiseStore::PARENT_ID] = $value->getId();
                return new DependentPromise($this->getData());
            case PromiseInterface::FULFILLED:
                $this[PromiseStore::RESULT] = $value->wait(false);
                return new FulfilledPromise($this->getData());
            case PromiseInterface::REJECTED:
                $this[PromiseStore::RESULT] = $value->wait(false);
                return new RejectedPromise($this->getData());
            default:
                throw new \RuntimeException('Wrong state: ' . $state) . '. ID = ' . $this->getId();
        }
    }

    public function reject($reason)
    {
        if ((is_object($reason) && $reason instanceof PromiseInterface)) {
            $reason = 'Reason is promise. ID = ' . $reason->getId();
        }
        if (!(is_object($reason) && $reason instanceof \Exception)) {
            set_error_handler(function ($number, $string) {
                throw new \UnexpectedValueException(
                'Reason cannot be converted to string.  ID: ' . $this->getId(), null, null
                );
            });
            try {
                //$reason can be converted to string
                $reasonStr = strval($reason);
                $reason = new \Exception($reasonStr);
            } catch (\Exception $exc) {
                //$reason can not be converted to string
                $reason = $exc;
            }
        }
        $this[PromiseStore::RESULT] = $reason;
        $rejectedPromise = new RejectedPromise($this->getData());
        return $rejectedPromise;
    }

    public function getState()
    {
        $data = $this->getData();
        $state = $data[PromiseStore::STATE];
        return $state;
    }

    public function wait($unwrap = true)
    {
        if ($unwrap) {
            return new PromiseException('Do not try to call wait(true)');
        }
        return $this;
    }

    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
        return new DependentPromise([
            PromiseStore::PARENT_ID => $this->getId(),
            PromiseStore::ON_FULFILLED => $onFulfilled,
            PromiseStore::ON_REJECTED => $onRejected
        ]);
    }

}
