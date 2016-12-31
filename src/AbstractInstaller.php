<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy;

use Interop\Container\ContainerInterface;
use zaboy\install\async\Entity\Installer as EntityInstaller;
use zaboy\install\async\Promise\Installer as PromiseInstaller;
use zaboy\install\Callback\Interruptor\Script\Installer as InterruptorScriptInstaller;
use zaboy\installer\Install\InstallerInterface;

/**
 * Installer class
 *
 * @category   Zaboy
 * @package    zaboy
 */
abstract class AbstractInstaller implements InstallerInterface
{

    const PRODACTION = 'prod';
    const TESTING = 'test';

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function reinstall()
    {
        $this->uninstall();
        $this->install();
    }
}



