<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\async\Promise;

use zaboy\async\Entity\Client;
use zaboy\async\Promise\Store as PromiseStore;
use zaboy\async\Promise\PromiseInterface;

/**
 * Client
 *
 * @category   async
 * @package    zaboy
 */
class Promise extends Client implements PromiseInterface
{

    /**
     * @var Store
     */
    protected $store;

    /**
     * @var string
     */
    protected $id;

    /**
     * Client constructor.
     *
     * @see https://github.com/domenic/promises-unwrapping/blob/master/docs/states-and-fates.md
     * @see https://github.com/promises-aplus/promises-spec
     *
     * @param string|array $data
     * @throws \LogicException
     */
    public function __construct($data = [])
    {
        parent::__construct($data, new PromiseStore());
    }

    public function wait($unwrap = true)
    {
        if (!$unwrap) {
            return $this->getEntity()->wait(false);
        }
    }

    public function resolve($value)
    {
        $id = $this->runTransaction('resolve', [$value]);
        return $id;
    }

    public function reject($value)
    {
        $id = $this->runTransaction('reject', [$value]);
        return $id;
    }

    /**
     * Returns the class name of Entity
     *
     * @return string
     */
    protected function getClass($data = null)
    {
        $namespace = '\\' . __NAMESPACE__ . '\\Promise\\';
        switch (true) {
            case empty($data) || !array_key_exists(PromiseStore::STATE, $data) ||
            $data[PromiseStore::STATE] === PromiseInterface::PENDING &&
            empty($data[PromiseStore::PARENT_ID]):
                return $namespace . 'Pending';
            case $data[PromiseStore::STATE] === PromiseInterface::FULFILLED:
                return $namespace . 'Fulfilled';
            case $data[PromiseStore::STATE] === PromiseInterface::REJECTED:
                return $namespace . 'Rejected';
            default:
                return $namespace . 'Dependent';
        }
    }

    public function getState()
    {
        return $this->getEntity()->getState();
    }

    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
        $id = $this->runTransaction('then', [ $onFulfilled, $onRejected]);
        return new static($id);
    }

    protected function runTransaction($methodName, $params = [])
    {
        try {
            $this->store->beginTransaction();
            $entity = $this->getEntity();
            $stateBefore = $entity->getState();
            $methodResult = call_user_func_array([$entity, $methodName], $params);
            $resultType = gettype($methodResult);
            switch ($resultType) {
                case 'object':
                    $stateAfter = $methodResult->getState();
                    $data = $methodResult->getData();
                    $id = $methodResult->getId();
                    unset($data[PromiseStore::ID]);
                    //or update
                    $where = [PromiseStore::ID => $id];
                    $number = $this->store->update($data, $where);
                    //or create a new one if absent
                    if (!$number) {
                        $this->store->insert($methodResult->getData());
                    }
                    $this->store->commit();
                    if ($stateBefore <> $stateAfter) {
                        $this->resolveDependent($methodResult->wait(false), $stateAfter === PromiseInterface::REJECTED);
                    }
                    return $id;
                case 'NULL':
                    $this->store->commit();
                    return $this->getId();
                default:
                    throw new \LogicException('Wrong type of result ' . $resultType);
            }
        } catch (\Exception $exc) {
            $this->store->rollback();
            $reason = 'Error while method  ' . $methodName . ' is running.' . PHP_EOL .
                    'Reason: ' . $exc->getMessage() . PHP_EOL .
                    ' Id: ' . $this->id;
            throw new \RuntimeException($reason, 0, $exc);
        }
    }

    protected function resolveDependent($result, $isRejected)
    {
        //are dependent promises exist?
        $rowset = $this->store->select([PromiseStore::PARENT_ID => $this->getId()]);
        $rowsetArray = $rowset->toArray();
        foreach ($rowsetArray as $dependentPromiseData) {
            $dependentPromiseId = $dependentPromiseData[PromiseStore::ID];
            $dependentPromise = new static($dependentPromiseId);
            try {
                if (!$isRejected) {
                    $dependentPromise->resolve($result);
                } else {
                    $dependentPromise->reject($result);
                }
            } catch (\Exception $exc) {
                throw new \RuntimeException(
                'Cannot ' . $isRejected ? 'reject' : 'resolve' .
                        '  dependent Promise: ' . $dependentPromiseId
                , 0, $exc);
            }
        }
    }

}
