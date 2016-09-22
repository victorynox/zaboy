<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\async\Entity;

use zaboy\async\Entity\Store as EntityStore;
use zaboy\async\Entity\Base;
use zaboy\async\Entity\Entity;

/**
 * Client
 *
 * @category   async
 * @package    zaboy
 */
abstract class Client extends Base
{

    /**
     * @var EntityStore
     */
    protected $store;

    /**
     * @var string
     */
    protected $id;

    /**
     * Client constructor.
     *
     * @param EntityStore $store
     * @param array|null $data
     */
    public function __construct($data = null, EntityStore $store = null)
    {
        parent::__construct();

        $this->store = $store;

        if (!is_array($data) && !empty($data) && !$this->isId($data)) {
            throw new \LogicException('Wrong format of specified data');
        }
        if ($this->isId($data)) {
            $this->id = $data;
            return;
        }
        if (is_array($data) || empty($data)) {
            $entity = $this->makeEntity($data);
            $this->id = $entity->getId();
        }
    }

    /**
     * Makes an Entity with specified data or a new Entity
     *
     * @param array|null $data
     * @return EntityAbstract
     */
    protected function makeEntity($data = null)
    {
        return new Entity($data);
    }

    /**
     * Returns an array created from data of entity stored in the Store .
     *
     * @param string|null $id
     * @param bool $lockRow
     * @return array mixed
     */
    protected function getStoredData($id = null, $lockRow = false)
    {
        $id = !$id ? $this->getId() : $id;
        $data = $lockRow ? $this->store->readAndLock($id) : $this->store->read($id);
        if (empty($data)) {
            throw new \LogicException(
            "There is no data in the store for id: $id"
            );
        } else {
            //Restore EntityObject from EntityId
            foreach ($data as $key => $value) {
                if ($key !== StoreAbstract::ID && $this->isId($value)) {
                    $data[$key] = new static($value);
                }
            }
            return $data;
        }
    }

    protected function getEntity($lockRow = false)
    {
        $data = $this->getStoredData(null, $lockRow);
        $entityClass = $this->getClass($data);
        $entity = new $entityClass($data);
        return $entity;
    }

    public function remove($id = null)
    {
        $id = !$id ? $this->getId() : $id;
        if (!$this->isId($id)) {
            throw new \LogicException(
            "There is not correct id: $id"
            );
        }
        $count = $this->store->delete([StoreAbstract::ID => $id]);
        return $count;
    }

    /**
     * Runs method od Entity with name $methodName and writes down result into the Store.
     *
     * If you don't wont to save result in the Store the method $methodName must return null.
     *
     * The method $methodName can receive not greater then two mixed parameters: $param1 and $param2.
     *
     * @param $methodName
     * @param mixed|null $param1
     * @param mixed|null $params2
     * @return string
     * @throws $this::EXCEPTION_CLASS
     */
    protected function runTransaction($methodName, $param1 = null, $params2 = null)
    {
        try {
            $errorMsg = "Can't start transaction for the method \"$methodName\"";
            $this->store->beginTransaction();
            //is row with this index exist?
            $errorMsg = "Can't execute the method \"readAndLock\" for $this->id";
            $data = $this->getStoredData($this->id, true);
            $errorMsg = "Cannot execute the method \"$methodName\". Entity does not exist.";
            if (is_null($data)) {
                throw new \Exception("Entity does not exist in the Store.");
            }
            $entityClass = $this->getClass();
            $entity = new $entityClass($data);
            $errorMsg = "Cannot execute the method \"$methodName\". Class: $entityClass";
            $dataReturned = call_user_func([$entity, $methodName], $param1, $params2);
            if (!is_null($dataReturned)) {
                $errorMsg = "Cannot execute the method \"store->update\".";
                $id = $dataReturned[StoreAbstract::ID];
                unset($dataReturned[StoreAbstract::ID]);
                //or update
                $number = $this->store->update($dataReturned, [StoreAbstract::ID => $id]);
                if (!$number) {
                    //or create a new one if absent
                    $dataReturned[StoreAbstract::ID] = $id;
                    $this->store->insert($dataReturned);
                }
            } else {
                $dataReturned = $data;
                $id = $this->id;
            }

            $this->store->commit();
            return $id;
        } catch (\Exception $e) {
            $this->store->rollback();
            $exceptionClass = $this::EXCEPTION_CLASS;
            throw new $exceptionClass($errorMsg . ' Id: ' . $this->id, 0, $e);
        }
    }

    /**
     * Returns the class name of Entity
     *
     * @return string
     */
    abstract protected function getClass($id = null);

    /**
     * Returns the Entity ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /*
     * Returns the Entity as array with unserialized properties
     */

    abstract public function toArray();

    /**
     * Returns the Store.
     *
     * @return StoreAbstract
     */
    public function getStore()
    {
        return $this->store;
    }

}
