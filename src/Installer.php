<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy;

error_reporting(E_ALL);
// Change to the project root, to simplify resolving paths
chdir(dirname(__DIR__));
require 'vendor/autoload.php';
$container = include 'config/container.php';

use zaboy\install\async\Entity\Installer as EntityInstaller;
use zaboy\install\async\Promise\Installer as PromiseInstaller;
use zaboy\install\Callback\Interruptor\Script\Installer as InterruptorScriptInstaller;

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

        $scriptInstaller = new InterruptorScriptInstaller();
        $scriptInstaller->install();

        $entityInstaller = new EntityInstaller();
        $entityInstaller->install();

        $promiseInstaller = new PromiseInstaller();
        $promiseInstaller->install();
    }

}

Installer::install();


