<?php

namespace zaboy\test\utils\Json;

use zaboy\Exception;
use zaboy\utils\Json\Coder as JsonCoder;

class CoderTest extends \PHPUnit_Framework_TestCase
{

    public function provider_SimpleType()
    {
        return array(
            array(false, 'false'),
            array(true, 'true'),
            //
            array(-30001, '-30001'),
            array(-1, '-1'),
            array(0, '0'),
            array(1, '1'),
            array(30001, '30001'),
            //
            array(-30001.00001, '-30001.00001'),
            array(0.0, '0', 0), //we get 0 - not 0.0
            array(30001.00001, '30001.00001'),
            //
            array('-30001', '"-30001"'),
            array('0', '"0"'),
            array('30001', '"30001"'),
            //
            array(
                'String строка !"№;%:?*(ХхЁ' . PHP_EOL,
                '"String \u0441\u0442\u0440\u043e\u043a\u0430 !\u0022\u2116;%:?*(\u0425\u0445\u0401\r\n"'
            ),
            //
            array(
                [],
                '[]'
            ),
            array(
                [1, 'a', ['array']],
                '[1,"a",["array"]]'
            ),
            array(
                [1 => 'string', 'array', 'next' => 'next string'],
                '{"1":"string","2":"array","next":"next string"}'
            ),
            array(
                [1, 2 => 2, 'next' => 'string', ['array'], [[1 => 'string', 'array', 'next' => 'next string']]],
                '{"0":1,"2":2,"next":"string","3":["array"],"4":[{"1":"string","2":"array","next":"next string"}]}',
            ),
            array(
                ['one' => 1, 'tow' => 2],
                '{"one":1,"tow":2}',
            ),
            //
            array(
                new \stdClass(),
                '{}',
                []
            ),
        );
    }

    /**
     * @dataProvider provider_SimpleType
     */
    public function testCoder_SimpleType($in, $jsonString, $out = null)
    {
        $out = isset($out) ? $out : $in; //usialy $out === $in

        $this->assertSame(
                $jsonString, JsonCoder::jsonEncode($in)
        );

        $this->assertSame(
                $out, JsonCoder::jsonDecode(JsonCoder::jsonEncode($in))
        );
    }

    public function provider_ObjectType()
    {
        $stdClass = new \stdClass();
        $stdClass->prop = 1;

        return array(
            array(
                $stdClass,
                '{"prop":1}',
                ['prop' => 1]
            ),
        );
    }

    /**
     * @dataProvider provider_ObjectType
     */
    public function testCoder_ObjectType($in, $jsonString, $out = null)
    {
        $out = isset($out) ? $out : $in; //usialy $out === $in

        $this->assertSame(
                $jsonString, JsonCoder::jsonEncode($in)
        );

        $this->assertEquals(
                $out, JsonCoder::jsonDecode(JsonCoder::jsonEncode($in))
        );
    }

}
