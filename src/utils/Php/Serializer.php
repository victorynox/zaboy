<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\utils\Php;

use mindplay\jsonfreeze\JsonSerializer;
use zaboy\utils\Json\Plugin\ExceptionSerializer;
use zaboy\utils\Json\Exception as JsonException;

/**
 *
 *
 * @category   utils
 * @package    zaboy
 * @todo set_error_handler in jsonEncode()
 */
class Serializer
{

    public static function phpSerialize($value)
    {
        //is Resource - Exception
        //is Simple - just Serialize
        //is Closure -  new SerializableClosure
        //is Array foreach recursion
        //is Object -
        //// is instanceof Serializable
        //// is support __sleep
        ////




        if (is_resource($value) || $value instanceof \Closure) {
            $class = is_object($value) ? ' with class ' . get_class($value) : '';
            throw new JsonException(
            'Data must be scalar or array or object,  ' .
            'but  type ' . gettype($value) . $class . ' given.'
            );
        }
        $serializer = new JsonSerializer();
        ExceptionSerializer::defineExceptionSerializer($value, $serializer);
        $serializedValue = $serializer->serialize($value);
        return $serializedValue;
    }

    public static function phpUnserialize($serializedValue)
    {
        $serializer = new JsonSerializer();
        ExceptionSerializer::defineExceptionUnserializer($serializedValue, $serializer);
        $value = $serializer->unserialize($serializedValue);
        return $value;
    }

}
