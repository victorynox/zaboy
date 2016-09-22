<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\Di\Example\InsideConstruct;

use zaboy\Di\InsideConstruct;

class PropertiesWithAliases
{

    public $propA;
    protected $propB;
    private $propC;

    public function __construct(&$useDi, $propA = null, $propB = null, $propC = null)
    {
        if ($useDi) {
            $useDi = InsideConstruct::initServices();
        } else {
            $this->propA = $propA;
            $this->propB = $propB;
            $this->propC = $propC;
        }
    }

}
