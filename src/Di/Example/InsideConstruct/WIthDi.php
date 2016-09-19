<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\Di\Example\InsideConstruct;

use Interop\Container\ContainerInterface;

class Class1
{

    public $propA;
    public $propB;
    public $propC;

    public function __construct($propA = null, $propB = null, $propC = null)
    {
        InsideConstruct::initServices();
    }

}

new Class1();



