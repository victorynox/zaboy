<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\async\Promise\Promise;

use zaboy\async\Promise\Store as PromiseStore;
use zaboy\async\Promise\Promise\FulfilledPromise;
use zaboy\async\Promise\Promise\RejectedPromise;
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
        $data[PromiseStore::STATE] = PromiseInterface::PENDING;
        parent::__construct($data);
    }

//
//    public function resolve($value)
//    {
//        $fulfilledPromise = new FulfilledPromise($this->getData(), $value);
//        return $fulfilledPromise->getData();
//    }
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


    public function getState()
    {
        $data = $this->getData();
        $state = $data[PromiseStore::STATE];
        return $state;
    }

}
