<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\async\Promise\Promise;

use zaboy\async\Promise\Store as PromiseStore;
use zaboy\async\Promise\Promise\Fulfilled as FulfilledPromise;
use zaboy\async\Promise\Promise\Rejected as RejectedPromise;
use zaboy\async\Promise\Promise\Pending as PendingPromise;
use zaboy\async\Entity\Entity;
use zaboy\async\Promise\PromiseInterface;

/**
 * DependentPromise
 *
 * @category   async
 * @package    zaboy
 */
class Dependent extends PendingPromise
{

    /**
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        parent::__construct($data);
        if (!(isset($data[PromiseStore::PARENT_ID]) && $this->isId($data[PromiseStore::PARENT_ID]))) {
            throw new \RuntimeException('Wromg PARENT_ID. ID = ' . $this->getId());
        }
        $this[PromiseStore::STATE] = PromiseInterface::PENDING;
        $this[PromiseStore::PARENT_ID] = $data[PromiseStore::PARENT_ID];
        $this[PromiseStore::ON_FULFILLED] = isset($data[PromiseStore::ON_FULFILLED]) ? $data[PromiseStore::ON_FULFILLED] : null;
        $this[PromiseStore::ON_REJECTED] = isset($data[PromiseStore::ON_REJECTED]) ? $data[PromiseStore::ON_REJECTED] : null;
    }

    public function resolve($value)
    {
        if (is_object($value) && $value instanceof PromiseInterface && $this[PromiseStore::PARENT_ID] === $value->getId()) {
            return null;
        }
        return parent::resolve($value);
    }

}
