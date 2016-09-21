<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\async\Entity;

use Zend\Db\Adapter\AdapterInterface;
use zaboy\Installer as ZaboyInstaller;
use zaboy\utils\Db\Mysql\TableManager;
use zaboy\async\Entity\Store as EntityStore;
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
    private $entityDbAdapter;

    public function __construct(AdapterInterface $entityDbAdapter = null)
    {
        //set $this->entityDbAdapter as $cotainer->get('entityDbAdapter');
        InsideConstruct::initServices();
    }

    public function install()
    {
        $tableManager = new TableManager($this->entityDbAdapter);
        $tableConfig = $this->getTableConfig();
        $tableName = EntityStore::TABLE_NAME;
        $tableManager->rewriteTable($tableName, $tableConfig);
    }

    protected function getTableConfig()
    {
        return [
            EntityStore::ID => [
                'field_type' => 'Varchar',
                'field_params' => [
                    'length' => 128,
                    'nullable' => false
                ]
            ]
        ];
    }

}
