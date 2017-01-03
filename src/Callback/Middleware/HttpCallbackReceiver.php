<?php
/**
 * Created by PhpStorm.
 * User: victorsecuring
 * Date: 30.12.16
 * Time: 3:10 PM
 */

namespace zaboy\Callback\Middleware;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use zaboy\Callback\Callback;
use zaboy\Callback\CallbackException;
use zaboy\Callback\Interruptor\Process;
use zaboy\Callback\InterruptorInterface;
use zaboy\Callback\PromiserInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Stratigility\MiddlewareInterface;

class HttpCallbackReceiver implements MiddlewareInterface
{

    /**
     * Process an incoming request and/or response.
     *
     * Accepts a server-side request and a response instance, and does
     * something with them.
     *
     * If the response is not complete and/or further processing would not
     * interfere with the work done in the middleware, or if the middleware
     * wants to delegate to another process, it can use the `$out` callable
     * if present.
     *
     * If the middleware does not return a value, execution of the current
     * request is considered complete, and the response instance provided will
     * be considered the response to return.
     *
     * Alternately, the middleware may return a response instance.
     *
     * Often, middleware will `return $out();`, with the assumption that a
     * later middleware will return a response.
     *
     * @param Request $request
     * @param Response $response
     * @param null|callable $out
     * @return null|Response
     */
    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        $callback = $request->getBody()->getContents();

        $paramsArray = is_null($callback) ? null : unserialize(base64_decode($callback));
        $callback = array_key_exists(Process::CALLBACK_KEY, $paramsArray) ?
            $paramsArray[Process::CALLBACK_KEY] : null;

        $value = array_key_exists(Process::VALUE_KEY, $paramsArray) ?
            $paramsArray[Process::VALUE_KEY] : null;

        try {
            switch ($callback) {
                case $callback instanceof PromiserInterface:
                    //todo Some
                case $callback instanceof InterruptorInterface:
                    //todo Some
                case is_callable($callback):
                    $callback = new Process($callback);
                    break;
                default :
                    throw new CallbackException('Callback is not callable');
            }

            $data = call_user_func($callback, $value);
            return new JsonResponse([
                'data' => $data,
                'status' => 'complete',
            ], 200);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'data' => $e->getMessage()
            ], 500);
        }

    }
}