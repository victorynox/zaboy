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
 * Promise
 *
 * @category   async
 * @package    zaboy
 */
class Pending extends Entity
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
        if ($value === $this) {
            $exc = new \UnexpectedValueException('TypeError. ID = ' . $this->getId());
            $this[PromiseStore::RESULT] = $exc;
            return new RejectedPromise($this->getData());
        }

        //Don't try rresolve with new value
        $storedValue = is_object($value) && $value instanceof PromiseInterface ? $value->getId() : $value;
        $isWrongValue = !is_null($this[PromiseStore::RESULT]) && $storedValue !== $this[PromiseStore::RESULT];
        if ($isWrongValue) {
            throw new \LogicException('The promise is already fulfilled.' . ' ID = ' . $this->getId());
        }

        $isDuplicateValue = !is_null($this[PromiseStore::RESULT]) && $storedValue === $this[PromiseStore::RESULT];
        if ($isDuplicateValue) {
            return null;
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
        $rejectedPromise = new RejectedPromise($this->getData(), $reason);
        return $rejectedPromise->getData();
    }

//
//    public function reject($reason)
//    {
//        $rejectedPromise = new RejectedPromise($this->getData(), $reason);
//        return $rejectedPromise->getData();
//    }
//
//    public function wait($unwrap = true)
//    {
//        return $this;
//    }
//
//    public function then(callable $onFulfilled = null, callable $onRejected = null)
//    {
//        $dependentPromise = new DependentPromise([], $this->getId(), $onFulfilled, $onRejected);
//        $dependentPromiseData = $dependentPromise->getData();
//        return $dependentPromiseData;
//    }
//
//    public function getResult()
//    {
//        return $this;
//    }
// =============== PromiseInterface =================

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
        $state = $this->getState();

        if ($state === PromiseInterface::FULFILLED || $state === PromiseInterface::REJECTED) {
            return $this[PromiseStore::RESULT];
        }

        //Pending promise
        if (is_null($this[PromiseStore::PARENT_ID])) {
            return $this;
        }
        //dependent promise
        $parentPromise = new Promise($this[PromiseStore::PARENT_ID]);
        return $parentPromise;
    }

}
