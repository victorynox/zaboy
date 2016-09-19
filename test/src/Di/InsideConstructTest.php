<?php

namespace zaboy\test\Di;

use Interop\Container\ContainerInterface;

class InsideConstructTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed
     */
    protected function setUp()
    {
        $this->container = '';
    }

    //==========================================================================

    public function testSerialize_ScalarType()
    {
        $this->assertEquals(1, 1);
    }

}
