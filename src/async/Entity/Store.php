<?php

namespace zaboy\async\Entity;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql;
use zaboy\utils\Db\Mysql\TableManager;
use \zaboy\Di\InsideConstruct;

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
    const ID = TableManager::ID;

    /**
     *
     * @var AdapterInterface
     */
    private $entityDbAdapter;

    public function __construct(AdapterInterface $entityDbAdapter = null)
    {
        //set $this->entityDbAdapter as $cotainer->get('entityDbAdapter');
        InsideConstruct::initServices();
        $adapter = $this->entityDbAdapter;
        unset($this->entityDbAdapter);

        $table = static::TABLE_NAME;
        parent::__construct($table, $adapter);
    }

    public function beginTransaction()
    {
        $db = $this->getAdapter();
        $db->getDriver()->getConnection()->beginTransaction();
    }

    public function commit()
    {
        $db = $this->getAdapter();
        $db->getDriver()->getConnection()->commit();
    }

    public function rollback()
    {
        $db = $this->getAdapter();
        $db->getDriver()->getConnection()->rollback();
    }

    public function readAndLock($id)
    {
        $identifier = self::ID;
        $db = $this->getAdapter();
        $queryStr = 'SELECT ' . Select::SQL_STAR
                . ' FROM ' . $db->platform->quoteIdentifier($this->getTable())
                . ' WHERE ' . $db->platform->quoteIdentifier($identifier) . ' = ?'
                . ' FOR UPDATE';

        $rowset = $db->query($queryStr, array($id));
        $data = $rowset->current();
        if (is_null($data)) {
            return null;
        } else {
            return $data->getArrayCopy();
        }
    }

    public function read($id)
    {
        $where = [self::ID => $id];
        $rowset = $this->select($where);
        $data = $rowset->current();
        if (!isset($data)) {
            return null;
        } else {
            return $data->getArrayCopy();
        }
    }

    public function insert($data)
    {
        return parent::insert($data);
    }

    public function update($data, $where = null)
    {
        return parent::update($data, $where);
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

}
