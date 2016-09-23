<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy;

// Change to the project root, to simplify resolving paths
chdir(dirname(__DIR__));
require 'vendor/autoload.php';
$container = include 'config/container.php';

use zaboy\utils\Db\Mysql\TableManager;
use zaboy\install\async\Entity\Installer as EntityInstaller;
use zaboy\install\async\Promise\Installer as PromiseInstaller;

/**
 * Installer class
 *
 * @category   Zaboy
 * @package    zaboy
 */
class Installer
{

    const PRODACTION = 'prod';
    const TESTING = 'test';

    public function __construct()
    {

    }

    public static function install()
    {
        $entityInstaller = new EntityInstaller();
        $entityInstaller->install();

        $promiseInstaller = new PromiseInstaller();
        $promiseInstaller->install();
    }

}

Installer::install();


