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

    public static function initServices($aliases = [])
    {
        $result = [];
        global $container;
        static::$container = static::$container ? static::$container : $container;
        if (!(isset(static::$container) && static::$container instanceof ContainerInterface)) {
            throw new \UnexpectedValueException(
            'global $contaner or InsideConstruct::$contaner'
            . ' must be inited'
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

            //Is param retrived?
            if (empty($args)) {
                //Has service in $container?
                if (!static::$container->has($paramName)) {
                    throw new \LogicException(
                    'Can not load service - "' . $paramName . '" for param - $' . $paramName
                    );
                }

                $paramValue = static::$container->get($paramName); // >getType()
                $paramClass = $refParam->getClass() ? $refParam->getClass()->getName() : null;
                if ($paramClass && !($paramValue instanceof $paramClass)) {
                    throw new \LogicException(
                    'Wrong type for service: ' . $paramName
                    );
                }
            } else {
                $paramValue = array_shift($args);
            }

            //setters
            $methodName = 'set' . ucfirst($paramName);
            $refMethod = $reflectionClass->hasMethod($methodName) ? $reflectionClass->getMethod($methodName) : null;
            if (isset($refMethod) && $refMethod->isPublic()) {
                $refMethod->invoke($object, $paramValue);
                $result[$paramName] = $paramValue;
                continue;
            }
            if (isset($refMethod) && ($refMethod->isPrivate() || $refMethod->isProtected())) {
                $refMethod->setAccessible(true);
                $refMethod->invoke($object, $paramValue);
                $result[$paramName] = $paramValue;
                $refMethod->setAccessible(false);
                continue;
            }

            //properties
            $refProperty = $reflectionClass->hasProperty($paramName) ? $reflectionClass->getProperty($paramName) : null;
            if (isset($refProperty) && $refProperty->isPublic()) {
                $refProperty->setValue($object, $paramValue);
                $result[$paramName] = $paramValue;
                continue;
            }
            if (isset($refProperty) && ( $refProperty->isPrivate() || $refProperty->isProtected())) {
                $refProperty->setAccessible(true);
                $refProperty->setValue($object, $paramValue);
                $result[$paramName] = $paramValue;
                $refProperty->setAccessible(false);
                continue;
            }
        }
        return $result;
    }

    public static function setContainer(ContainerInterface $container)
    {
        static::$container = $container;
    }

}
