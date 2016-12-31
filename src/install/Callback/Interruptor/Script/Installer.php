<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\install\Callback\Interruptor\Script;

use Interop\Container\ContainerInterface;
use zaboy\AbstractInstaller;
use zaboy\Callback\Interruptor\Process;
use zaboy\installer\Install\InstallerInterface;

/**
 * Installer class
 *
 * @category   Zaboy
 * @package    zaboy
 */
class Installer extends AbstractInstaller
{
    public function install()
    {
        if (!file_exists(getcwd() . DIRECTORY_SEPARATOR . Process::PATH_SCRIPT_WWW . Process::FILE_NAME)) {
            @mkdir(Process::PATH_SCRIPT_WWW, 0777, true);
            copy(
                    getcwd() . DIRECTORY_SEPARATOR . Process::PATH_SCRIPT_SRC . Process::FILE_NAME
                    , getcwd() . DIRECTORY_SEPARATOR . Process::PATH_SCRIPT_WWW . Process::FILE_NAME
            );
        }
        if (!file_exists(Process::PATH_SCRIPT_WWW . Process::FILE_NAME)) {
            throw new \RuntimeException(
            'Can not create file: '
            . getcwd() . DIRECTORY_SEPARATOR . Process::PATH_SCRIPT_WWW . Process::FILE_NAME
            );
        }
    }

    /**
     * Clean all installation
     * @return void
     */
    public function uninstall()
    {
        if(file_exists(getcwd() . DIRECTORY_SEPARATOR . Process::PATH_SCRIPT_WWW . Process::FILE_NAME)) {
            unlink(getcwd() . DIRECTORY_SEPARATOR . Process::PATH_SCRIPT_WWW . Process::FILE_NAME);
        }
    }
}
