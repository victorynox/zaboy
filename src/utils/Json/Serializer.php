<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\utils\Json;

use mindplay\jsonfreeze\JsonSerializer;
use zaboy\utils\Json\Coder as JsonCoder;
use zaboy\utils\Json\Exception as JsonException;

/**
 *
 *
 * @category   utils
 * @package    zaboy
 * @todo set_error_handler in jsonEncode()
 * @todo use is_a(get_class($value), '\Exception', true) in isClassException()
 */
class Serializer
{

    public static function jsonSerialize($value)
    {
        $serializer = new JsonSerializer();
        $serializer->defineSerialization('Exception', [get_class(), 'serializeException'], [get_class(), 'unserializeException']);

        //if (is_object($value) && is_a(get_class($value), '\Exception', true)) {
        if (is_object($value) && static::isClassException(get_class($value))) {
            $serializer->defineSerialization(get_class($value), [get_class(), 'serializeException'], [get_class(), 'unserializeException']);
        }
        $serializedValue = $serializer->serialize($value);
        return $serializedValue;
    }

    public static function jsonUnserialize($serializedValue)
    {
        $serializer = new JsonSerializer();
        $jsonDecoded = JsonCoder::jsonDecode($serializedValue);

        //if (isset($jsonDecoded[JsonSerializer::TYPE]) && is_a($jsonDecoded[JsonSerializer::TYPE], '\Exception', true)) {
        if (isset($jsonDecoded[JsonSerializer::TYPE]) && static::isClassException($jsonDecoded[JsonSerializer::TYPE])) {
            $serializer->defineSerialization($jsonDecoded[JsonSerializer::TYPE], [get_class(), 'serializeException'], [get_class(), 'unserializeException']);
        }

        $value = $serializer->unserialize($serializedValue);
        return $value;
    }

    public static function serializeException(\Exception $exception)
    {
        $data = array(
            JsonSerializer::TYPE => get_class($exception),
            "message" => $exception->getMessage(),
            "code" => $exception->getCode(),
            "line" => $exception->getLine(),
            "file" => $exception->getFile(),
            "prev" => $exception->getPrevious(),
        );
        return $data;
    }

    public static function unserializeException($data)
    {
        if (!isset($data["prev"])) {
            $exc = new $data[JsonSerializer::TYPE]($data["message"], $data["code"], null);
        } else {
            $prev = static::unserializeException($data["prev"]);
            $exc = new $data[JsonSerializer::TYPE]($data["message"], $data["code"], $prev);
        }
        $class = new \ReflectionClass($data[JsonSerializer::TYPE]);
        $properties = $class->getProperties();
        foreach ($properties as $prop) {
            if ($prop->getName() === "line" || $prop->getName() === "file") {
                $prop->setAccessible(true);
                $prop->setValue($exc, $data[$prop->getName()]);
                $prop->setAccessible(false);
            }
        }
        return $exc;
    }

    protected static function isClassException($className)
    {
        // return (is_object($value) && is_a($className, '\Exception', true));
        return substr($className, strlen($className) - strlen('Exception')) === 'Exception';
    }

}
