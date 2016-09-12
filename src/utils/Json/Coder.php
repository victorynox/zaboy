<?php

/**
 * Zaboy lib (http://zaboy.org/lib/)
 *
 * @copyright  Zaboychenko Andrey
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace zaboy\utils\Json;

use zaboy\utils\Json\Exception as JsonException;

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
        $result = json_decode((string) $data, 1); //json_decode($data);
        if (JSON_ERROR_NONE !== json_last_error()) {
            $jsonErrorMsg = json_last_error_msg();
            json_encode(null);  // Clear json_last_error()
            throw new JsonException(
            'Unable to decode data from JSON - ' . $jsonErrorMsg
            );
        }
        return $result;
    }

    public static function jsonEncode($data)
    {
        json_encode(null); // Clear json_last_error()
        $result = json_encode($data, 79);
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
