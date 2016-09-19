<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\Di\Example\InsideConstruct;

use Interop\Container\ContainerInterface;
use zaboy\Di\InsideConstruct;

class PublicProtectedPrivate
{

    public $propA;
    protected $propB;
    private $propC;

    public function __construct($propA = null, $propB = null, $propC = null)
    {
        InsideConstruct::initServices();
    }

}

class B
{

    public $propA;
    protected $propB;
    private $private;

    public function __construct(\stdClass $propA = null, $propB = null, $propC = null)
    {
        InsideConstruct::initServices();
    }

}
