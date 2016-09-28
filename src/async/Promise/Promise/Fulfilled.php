<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\async\Promise\Promise;

use zaboy\async\Promise\Store as PromiseStore;
use zaboy\async\Promise\Promise\Pending as PendingPromise;
use zaboy\async\Promise\Promise\Rejected as RejectedPromise;
use zaboy\async\Promise\Promise\Dependent as DependentPromise;
use zaboy\async\Entity\Entity;
use zaboy\async\Promise\PromiseInterface;

/**
 * FulfilledPromise
 *
 * @category   async
 * @package    zaboy
 */
class Fulfilled extends PendingPromise
{

    /**
     *
     * @param array $promiseData
     */
    public function __construct($data = [])
    {
        parent::__construct($data);
        if (!array_key_exists(PromiseStore::RESULT, $data)) {
            throw new \RuntimeException('Wromg RESULT type - promise. ID = ' . $this->getId());
        }
        $result = $data[PromiseStore::RESULT];

        if (is_object($result) && $result instanceof PromiseInterface) {
            throw new \RuntimeException('Can not fullfill without result value. ID = ' . $this->getId());
        }
        $this[PromiseStore::RESULT] = $result;
        $this[PromiseStore::STATE] = PromiseInterface::FULFILLED;
        $this[PromiseStore::ON_FULFILLED] = null;
        $this[PromiseStore::ON_REJECTED] = null;
        $this[PromiseStore::PARENT_ID] = null;
    }

    public function reject($reason)
    {
        throw new \RuntimeException('Cannot reject a fulfilled promise.  ID: ' . $this->getId());
    }

}
