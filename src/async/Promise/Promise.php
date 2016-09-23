<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\async\Promise;

use zaboy\async\Entity\Store as EntityStore;
use zaboy\async\Entity\Base;
use zaboy\Di\InsideConstruct;
use zaboy\async\Entity\Entity;
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
     * @param string|array $data
     * @param EntityStore $entityStore
     * @throws \LogicException
     */
    public function __construct($data = null)
    {
        parent::__construct($data, new PromiseStore());
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
            case $data === null:
                return $namespace . 'Pending';
//            case $data[Store::STATE] === PromiseInterface::FULFILLED:
//                return '\zaboy\async\Promise\Promise\FulfilledPromise';
//            case $data[Store::STATE] === PromiseInterface::FULFILLED:
//                return '\zaboy\async\Promise\Promise\FulfilledPromise';
//            case $data[Store::STATE] === PromiseInterface::REJECTED:
//                return '\zaboy\async\Promise\Promise\RejectedPromise';
            case $data[PromiseStore::PARENT_ID] === null:
                return $namespace . 'Pending';
//            default:
//                return '\zaboy\async\Promise\Promise\DependentPromise';
        }
    }

// =============== PromiseInterface =================

    public function getState()
    {
        return $this->getEntity()->getState();
    }

}
