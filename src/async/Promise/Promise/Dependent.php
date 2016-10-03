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
use zaboy\async\Promise\PromiseInterface;
use zaboy\async\Promise\Promise;

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

        $onFulfilledError = !(is_null($this[PromiseStore::ON_FULFILLED]) || is_callable($this[PromiseStore::ON_FULFILLED]));
        $onRrejecedError = !(is_null($this[PromiseStore::ON_REJECTED]) || is_callable($this[PromiseStore::ON_REJECTED]));
        if ($onFulfilledError || $onRrejecedError) {
            throw new \UnexpectedValueException(
            ($onFulfilledError ? 'ON_FULFILLED' : 'ON_REJECTED') . ' must be coallable'
            );
        }
    }

    public function resolve($value)
    {
        $resultEntity = parent::resolve($value);
        $state = $resultEntity->getState();
        switch ($state) {
            case PromiseInterface::PENDING:
                //parent promise is resolved by promise - we has new parent promise
                $this[PromiseStore::PARENT_ID] = $resultEntity[PromiseStore::PARENT_ID];
                return $this;
            case PromiseInterface::REJECTED:
                //parent promise is rejected
                return $resultEntity;
            case PromiseInterface::FULFILLED:
                //parent promise is fulfilled - we just resolve (there is not ON_FULFILLED)
                $onFulfilledCallback = $this[PromiseStore::ON_FULFILLED];
                if (is_null($onFulfilledCallback)) {
                    return $resultEntity;
                }
                try {
                    $result = call_user_func($onFulfilledCallback, $value);
                } catch (\Exception $ex) {
                    return parent::reject($ex);
                }
                return parent::resolve($result);
        }
    }

    public function reject($value)
    {
        $resultEntity = parent::reject($value);
        $onRejectedCallback = $this[PromiseStore::ON_REJECTED];
        if (is_null($onRejectedCallback)) {
            return $resultEntity;
        }
        try {
            $reason = $resultEntity->wait(false);
            $result = call_user_func($onRejectedCallback, $reason);
        } catch (\Exception $exc) {
            return parent::reject($exc);
        }
        return parent::resolve($result);
    }

}
