<?php
/**
 * Created by PhpStorm.
 * User: victorsecuring
 * Date: 30.12.16
 * Time: 3:33 PM
 */

namespace zaboy\Callback\Interruptor;


use Opis\Closure\SerializableClosure;
use zaboy\Callback\Callback;
use zaboy\Callback\CallbackException;
use zaboy\Callback\InterruptorInterface;
use Zend\Http\Client;
use Zend\Json\Json;

class Http extends InterruptorAbstract implements InterruptorInterface
{
    const CALLBACK_KEY = 'callback';
    const VALUE_KEY = 'value';
    const STDOUT_KEY = 'stdout';

    protected $url;

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
        $job = new Job($this->getCallback(), $value);

        $serializedJob = $job->serializeBase64();

        $result = [];

        $client = $this->initHttpClient();
        $client->setRawBody($serializedJob);

        //$result[self::STDOUT_KEY] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('stdout_', 1);
        //$client->setStream($result[self::STDOUT_KEY]);

        $response = $client->send();

        $result['data'] = $this->jsonDecode($response->getBody());
        $result[static::MACHINE_NAME_KEY] = constant(static::ENV_VAR_MACHINE_NAME);
        $result[static::INTERRUPTOR_TYPE_KEY] = static::class;
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

    /**
     * @param $data
     * @return mixed
     * @throws CallbackException
     */
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
}