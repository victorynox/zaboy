<?php

namespace zaboy\test\Di;

require_once './src/Di/Example/InsideConstruct/WIthInnerDi.php';

use zaboy\Di\Example\InsideConstruct\PublicProtectedPrivate;
use Interop\Container\ContainerInterface;
use zaboy\Di\InsideConstruct;
use Zend\ServiceManager\ServiceManager;

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
        $this->container = $this->getMock(ContainerInterface::class);
        InsideConstruct::setContainer($this->container);
    }

    //==========================================================================

    public function testInitServices_PublicProtectedPrivate()
    {
        $mapHas = [
            ['propA', true],
            ['propB', true],
            ['propC', false],
        ];
        $this->container->method('has')
                ->will($this->returnValueMap($mapHas));

        $mapGet = [
            ['propA', new \stdClass()],
            ['propB', new \ArrayObject()]
        ];
        $this->container->method('get')
                ->will($this->returnValueMap($mapGet));

        $tested = new PublicProtectedPrivate();
        $expected = new PublicProtectedPrivate(new \stdClass(), new \ArrayObject(), null);

        $this->assertEquals($expected, $tested);
    }

}
