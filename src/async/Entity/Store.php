<?php

namespace zaboy\async\Entity;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql;
use zaboy\rest\TableGateway\TableManagerMysql as TableManager;
use zaboy\res\Di\InsideConstruct;
use zaboy\rest\DataStore\Interfaces\ReadInterface;

/**
 * Store
 *
 * @category   async
 * @package    zaboy
 */
class Store extends TableGateway
{

    const TABLE_NAME = 'entity';

    /**
     * Primary key column name - 'id'
     * id has specific structoure - prefix__1234567890_12346__jljkHU6h4sgvYu...n67
     * where __1234567890_ is UTC creation time.
     */
    const ID = ReadInterface::DEF_ID;

    /**
     *
     * @var bool
     */
    protected $isInTransaction;

    public function __construct(AdapterInterface $entityDbAdapter = null)
    {
        //set $this->entityDbAdapter as $cotainer->get('entityDbAdapter');
        $services = InsideConstruct::initServices(['entityDbAdapter']);
        $adapter = $services['entityDbAdapter'];
        $table = static::TABLE_NAME;
        $this->isInTransaction = false;
        parent::__construct($table, $adapter);
    }

    public function beginTransaction()
    {
        if ($this->isInTransaction) {
            return;
        }
        $db = $this->getAdapter();
        $db->getDriver()->getConnection()->beginTransaction();
        $this->isInTransaction = true;
    }

    public function commit()
    {
        if ($this->isInTransaction) {
            $db = $this->getAdapter();
            $db->getDriver()->getConnection()->commit();
            $this->isInTransaction = false;
        } else {
            throw new \LogicException('Commit without Transaction');
        }
    }

    public function rollback($isInTransaction = true)
    {
        if ($this->isInTransaction) {
            $db = $this->getAdapter();
            $db->getDriver()->getConnection()->rollback();
            $this->isInTransaction = false;
        } else {
            throw new \LogicException('Rollback without Transaction');
        }
    }

    public function read($id)
    {
        $identifier = self::ID;
        $db = $this->getAdapter();
        $queryStr = 'SELECT ' . Select::SQL_STAR
                . ' FROM ' . $db->platform->quoteIdentifier($this->getTable())
                . ' WHERE ' . $db->platform->quoteIdentifier($identifier) . ' = ?';
        $queryStr = $this->isInTransaction ? $queryStr . ' FOR UPDATE' : $queryStr;

        $rowset = $db->query($queryStr, array($id));
        $data = $rowset->current();
        if (is_null($data)) {
            return null;
        } else {
            return $this->restoreData($data->getArrayCopy());
        }
    }

    public function insert($data)
    {
        return parent::insert($this->prepareData($data));
    }

    public function update($data, $where = null)
    {
        return parent::update($this->prepareData($data), $where);
    }

    public function count($where = [])
    {
        $db = $this->getAdapter();
        $sql = new Sql\Sql($db);
        $select = $sql->select()
                ->from($this->getTable())
                ->columns(array('count' => new Sql\Expression('COUNT(*)')))
                ->where($where);
        $statement = $sql->prepareStatementForSqlObject($select);
        $rowset = $statement->execute();
        return $rowset->current()['count'];
    }

    protected function prepareData(array $data, $fild = null)
    {
        if (isset($fild) && array_key_exists($fild, $data)) {
            return [$fild => $data[$fild]];
        }
        if (isset($fild)) {
            throw new \RuntimeException('Can not prepare fild' . $fild);
        }
        foreach ($data as $key => $value) {
            $preparedFild = $this->prepareData($data, $key); //['columnName => serializedValue]
            unset($data[$key]);
            $columnName = array_keys($preparedFild)[0];
            $data[$columnName] = $preparedFild[$columnName];
        }
        return $data;
    }

    protected function restoreData(array $data, $columnName = null)
    {
        if (isset($columnName) && array_key_exists($columnName, $data)) {
            return [$columnName => $data[$columnName]];
        }
        if (isset($columnName)) {
            throw new \RuntimeException('Can not restore column ' . $columnName);
        }
        foreach ($data as $key => $value) {
            $restoredFild = $this->restoreData($data, $key); //['fildName' => serializedValue]
            unset($data[$key]);
            $fild = array_keys($restoredFild)[0];
            $data[$fild] = $restoredFild[$fild];
        }
        return $data;
    }

}
