<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\install\async\Promise;

use Zend\Db\Adapter\AdapterInterface;
use zaboy\Installer as ZaboyInstaller;
use zaboy\utils\Db\Mysql\TableManager;
use zaboy\async\Promise\Store as PromiseStore;
use zaboy\Di\InsideConstruct;

/**
 * Installer class
 *
 * @category   Zaboy
 * @package    zaboy
 */
class Installer
{

    /**
     *
     * @var AdapterInterface
     */
    private $promiseDbAdapter;

    public function __construct(AdapterInterface $promiseDbAdapter = null)
    {
        //set $this->entityDbAdapter as $cotainer->get('entityDbAdapter');
        InsideConstruct::initServices();
    }

    public function install()
    {
        $tableManager = new TableManager($this->promiseDbAdapter);
        $tableConfig = $this->getTableConfig();
        $tableName = PromiseStore::TABLE_NAME;
        $tableManager->rewriteTable($tableName, $tableConfig);
    }

    protected function getTableConfig()
    {
        return [
            PromiseStore::ID => [
                'field_type' => 'Varchar',
                'field_params' => [
                    'length' => 128,
                    'nullable' => false
                ]
            ],
            PromiseStore::STATE => [
                'field_type' => 'Varchar',
                'field_params' => [
                    'length' => 128,
                    'nullable' => false
                ]
            ],
            PromiseStore::RESULT => [
                'field_type' => 'Varchar',
                'field_params' => [
                    'length' => 65000,
                    'nullable' => true
                ]
            ],
            PromiseStore::ON_FULFILLED => [
                'field_type' => 'Blob',
                'field_params' => [
                    'length' => 65000,
                    'nullable' => true
                ]
            ],
            PromiseStore::ON_REJECTED => [
                'field_type' => 'Blob',
                'field_params' => [
                    'length' => 65000,
                    'nullable' => true
                ]
            ],
            PromiseStore::PARENT_ID => [
                'field_type' => 'Varchar',
                'field_params' => [
                    'length' => 128,
                    'nullable' => true
                ]
            ],
        ];
    }

}
