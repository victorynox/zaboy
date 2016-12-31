<?php
/**
 * Created by PhpStorm.
 * User: victorsecuring
 * Date: 30.12.16
 * Time: 3:33 PM
 */

namespace zaboy\Callback\Interruptor;


use zaboy\Callback\Callback;
use zaboy\Callback\CallbackException;
use zaboy\rest\DataStore\DataStoreException;
use Zend\Http\Client;
use Zend\Json\Json;

class Http extends Callback
{
    protected $url;

    const CALLBACK_KEY = 'callback';

    const VALUE_KEY = 'value';

    public function __construct(callable $callback, $url)
    {
        parent::__construct($callback);

        $this->url = rtrim(trim($url), '/');
        /*if (is_array($options)) {
            if (isset($options['login']) && isset($options['password'])) {
                $this->login = $options['login'];
                $this->password = $options['password'];
            }
            $supportedKeys = [
                'maxredirects',
                'useragent',
                'timeout',
            ];
            $this->options = array_intersect_key($options, array_flip($supportedKeys));
        }*/
    }

    public function __invoke($value)
    {
        $arrayParams = [
            self::VALUE_KEY => $value,
            self::CALLBACK_KEY => $this->getCallback()
        ];

        $serializedParams = serialize($arrayParams);
        $params64 = base64_encode($serializedParams);

        /*  $this->options['outputstream'] = isset($this->options['outputstream']) ?
            $this->options['outputstream'] : sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('stdout_', 1);

        $result[self::STDOUT_KEY] = $this->options['outputstream'];*/

        $client = $this->initHttpClient();
        $client->setRawBody($params64);

        $response = $client->send();
        $result = $this->jsonDecode($response->getBody());

        return $result;
    }

    protected function jsonDecode($data)
    {
        json_encode(null); // Clear json_last_error()
        $result = Json::decode($data, Json::TYPE_ARRAY); //json_decode($data);
        if (JSON_ERROR_NONE !== json_last_error()) {
            $jsonErrorMsg = json_last_error_msg();
            json_encode(null);  // Clear json_last_error()
            throw new CallbackException(
                'Unable to decode data from JSON - ' . $jsonErrorMsg
            );
        }
        return $result;
    }

    /**
     *
     * @return Client
     */
    protected function initHttpClient()
    {
        $httpClient = new Client($this->url);
        $headers['Content-Type'] = 'text/text';
        $headers['Accept'] = 'application/json';
        $httpClient->setHeaders($headers);
        $httpClient->setMethod('POST');
        return $httpClient;
    }
}