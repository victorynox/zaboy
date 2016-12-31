<?php
/**
 * Created by PhpStorm.
 * User: victorsecuring
 * Date: 30.12.16
 * Time: 5:17 PM
 */

namespace zaboy\test\Interruptor\Callback;


use zaboy\Callback\Interruptor\Http;
use zaboy\test\Callback\CallbackTestDataProvider;


class HttpTest extends CallbackTestDataProvider
{

    protected $url;

    public function setUp()
    {
        $container = include 'config/container.php';
        $this->url = $container->get('config')['httpInterruptor']['url'];
    }

    /**
     * @param $callable
     * @param $val
     * @param $expected
     * @dataProvider provider_mainType()
     */
    public function test_httpInterruptor($callable, $val, $expected)
    {
        $httpInteraptor = new Http($callable, $this->url);
        $result = $httpInteraptor($val);
        $this->assertEquals($expected, $result['data']);
    }


    /**
     * @param $callable
     * @param $val
     * @param $expected
     * @dataProvider provider_insertedType()
     */
    public function test_insertedCallable($callable, $val, $expected)
    {
        $httpInteraptor = new Http($callable, $this->url);
        $result = $httpInteraptor($val);
        $this->assertEquals($expected, $result['data']);
    }
}
