<?php

namespace zaboy\test\utils\Db\Mysql;

use zaboy\utils\Db\Mysql\TableManager;
use Interop\Container\ContainerInterface;

class TableManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Returner
     */
    protected $object;

    /**
     * @var Zend\Db\Adapter\Adapter
     */
    protected $adapter;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     *
     * @var string
     */
    protected $tableName;

    /**
     *
     * @var array
     */
    protected $config = [
        'id' => [
            'field_type' => 'Integer',
            'field_params' => [
                'options' => ['autoincrement' => true]
            ]
        ],
        'name' => [
            'field_type' => 'Varchar',
            'field_params' => [
                'length' => 10,
                'nullable' => true,
                'default' => 'what?'
            ]
        ]
    ];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {

        $this->container = include './config/container.php';
        $this->adapter = $this->container->get('db');
        $this->tableName = 'test__table_manager';

        $this->object = new TableManager($this->adapter);
        if ($this->object->hasTable($this->tableName)) {
            $this->object->deleteTable($this->tableName);
        }
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    public function testTableManagerMysql_Create()
    {
        $this->object->createTable($this->tableName, $this->config);

        $this->assertSame(
                '    With columns: ' . PHP_EOL .
                '        id -> int' . PHP_EOL .
                '        name -> varchar' . PHP_EOL . PHP_EOL .
                '    With constraints: ' . PHP_EOL .
                '        _zf_test__table_manager_PRIMARY -> PRIMARY KEY' . PHP_EOL .
                '            column: id'
                , $this->object->getTableInfoStr($this->tableName)
        );
    }

}
