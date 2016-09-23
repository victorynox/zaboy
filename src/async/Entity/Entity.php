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

/**
 * Entity
 *
 * @category   async
 * @package    zaboy
 */
class Entity extends Base
{

    /**
     * @var array
     */
    public $data;

    /**
     * Entity constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        parent::__construct();

        if (!isset($data[EntityStore::ID])) {
            $data[EntityStore::ID] = $this->makeId();
        }
        $this->setData($data);
    }

    /**
     * Returns the ID of Entity
     *
     * @return mixed
     */
    public function getId()
    {
        $data = $this->getData();
        if (isset($data[EntityStore::ID])) {
            return $data[EntityStore::ID];
        } else {
            throw new \LogicException(
            "ID is not set."
            );
        }
    }

    /**
     * Returns the raw data of Entity
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->data)) {
            return $this->data;
        } else {
            throw new \LogicException(
            "Data is not set."
            );
        }
    }

    /**
     *
     * @param array $data
     * @return \zaboy\async\Entity\Entity
     * @throws \LogicException
     */
    protected function setData($data)
    {
        if (is_array($data) && isset($data[EntityStore::ID]) && $this->isId($data[EntityStore::ID])) {
            $this->data = $data;
            return $this;
        } else {
            throw new \LogicException(
            "Wrong data. \$data must be an array with 'id' key."
            );
        }
    }

}
