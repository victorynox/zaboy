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
class Promise extends Client //implements PromiseInterface
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

// =============== PromiseInterface =================

    public function getState()
    {
        return $this->getEntity()->getState();
    }

}
