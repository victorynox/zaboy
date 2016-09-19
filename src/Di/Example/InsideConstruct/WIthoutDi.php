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
        $this->propA = $propA;
        $this->propB = $propB;
        $this->propC = $propC;
    }

}

/* @var $contaner ContainerInterface */
global $contaner;
$propA = $contaner->has('propA') ? $contaner->get('propA') : null;
$propB = $contaner->has('propB') ? $contaner->get('propB') : null;
$propC = $contaner->has('propC') ? $contaner->get('propC') : null;

new Class1($propA, $propB, $propC);



