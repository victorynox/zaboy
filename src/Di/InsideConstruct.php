<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\Di;

use Interop\Container\ContainerInterface;

class InsideConstruct
{

    /**
     * Use next in head af scripts
     * <code>
     * require 'vendor/autoload.php';
     * $container = include 'config/container.php';
     * //add:
     * InsideConstruct::setContainer( $container )
     * <code>
     *
     * @var ContainerInterface
     */
    protected static $container = null;

    public static function initServices()
    {
        global $container;
        $container = $container ? $container : static::$container;
        if (!(isset($container) && $container instanceof ContainerInterface)) {
            throw new \UnexpectedValueException(
            'global $contaner or InsideConstruct::$contaner'
            . 'must be inited'
            );
        }
        //Who call me?;
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        $className = $trace[1]['class'];
        $reflectionClass = new \ReflectionClass($className);
        /* @var $reflectionClass \ReflectionClass */
        $object = $trace[1]['object'];
        $args = $trace[1]['args'];
        //I need your __construct params
        $refConstruct = $reflectionClass->getConstructor(); //$reflectionClass->getMethod('__construct');
        if (!isset($refConstruct)) {
            throw new \LengthException(
            'You must call InsideConstruct::initServices() inside Construct only'
            );
        }
        $refParams = $refConstruct->getParameters();
        // $refParams array of ReflectionParameter
        foreach ($refParams as $refParam) {
            /* @var $refParam \ReflectionParameter */
            $paramName = $refParam->getName();

            //Which are have  service  and not retrived in __construct
            if (!array_key_exists($paramName, $args) && $container->has($paramName)) {
                $paramValue = $container->get($paramName); // >getType()
                $paramClass = $refParam->getClass() ? $refParam->getClass()->getName() : null;
                if ($paramClass && !($paramValue instanceof $paramClass)) {
                    throw new \LogicException(
                    'Wrong type for service: ' . $paramName
                    );
                }
                $refProperty = $reflectionClass->getProperty($paramName);
                if (isset($refProperty) && $refProperty->isPublic()) {
                    $refProperty->setValue($object, $paramValue);
                }
                if (isset($refProperty) && $refProperty->isPrivate() || $refProperty->isProtected()) {
                    $refProperty->setAccessible(true);
                    $refProperty->setValue($object, $paramValue);
                    $refProperty->setAccessible(false);
                }
            }
        }
    }

    public static function setContainer(ContainerInterface $container)
    {
        static::$container = $container;
    }

}
