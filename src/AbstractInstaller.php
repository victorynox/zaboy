<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy;

use Interop\Container\ContainerInterface;
use zaboy\Callback\Interruptor\Process;
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
        if (!getenv('APP_ENV') || !getenv(Process::SERVICE_MACHINE_NAME_KEY)) {
            throw new Exception("Environment variable not set! Check 'APP_ENV' and " . Process::SERVICE_MACHINE_NAME_KEY . ".");
        }
        $this->container = $container;
    }

    public function reinstall()
    {
        $this->uninstall();
        $this->install();
    }
}



