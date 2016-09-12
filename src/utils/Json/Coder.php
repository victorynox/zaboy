<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\utils\Json;

use zaboy\utils\Json\Exception as JsonException;
use Zend\Json\Json;

/**
 *
 *
 * @category   utils
 * @package    zaboy
 * @todo set_error_handler in jsonEncode()
 */
class Coder
{

    public static function jsonDecode($data)
    {
        json_encode(null); // Clear json_last_error()
        $result = json_decode((string) $data, Json::TYPE_ARRAY); //json_decode($data);
        if (JSON_ERROR_NONE !== json_last_error()) {
            $jsonErrorMsg = json_last_error_msg();
            json_encode(null);  // Clear json_last_error()
            throw new JsonException(
            'Unable to decode data from JSON - ' . $jsonErrorMsg . PHP_EOL .
            'JSON string: ' . PHP_EOL . $data
            );
        }
        return $result;
    }

    /**
     *
     * @param mix $data
     * @return string
     * @throws JsonException
     * @see https://php.ru/manual/function.json-encode.html
     */
    public static function jsonEncode($data)
    {
        json_encode(null); // Clear json_last_error()
        $result = json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);
        if (JSON_ERROR_NONE !== json_last_error()) {
            $jsonErrorMsg = json_last_error_msg();
            json_encode(null);  // Clear json_last_error()
            throw new JsonException(
            'Unable to encode data to JSON - ' . $jsonErrorMsg
            );
        }
        return $result;
    }

}
