<?php

namespace zaboy\test\utils\Json;

use zaboy\Exception;
use zaboy\utils\Json\Serializer as JsonSerializer;

class SerializerTest extends \PHPUnit_Framework_TestCase
{

    public function provider_testCoder()
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
                '"String \u0441\u0442\u0440\u043e\u043a\u0430 !\"\u2116;%:?*(\u0425\u0445\u0401\r\n"'
            //  '"String \u0441\u0442\u0440\u043e\u043a\u0430 !\u0022\u2116;%:?*(\u0425\u0445\u0401\r\n"' - in Json\Coder
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
//
// it will not work. Array must be list or association array for convert to object
// ===============================================================================
//            array(
//                [1 => 'string', 'array', 'next' => 'next string'],
//                <<<JSON
//{
//  1: "string",
//  2: "array",
//  "next": "next string"
//}
//JSON
//'{"1":"string","2":"array","next":"next string"}' - in Json\Coder
//          ),
            //
                //
//
// it will not work. Array must be list or association array for convert to object
// ===============================================================================
//            array(
//                [1, 2 => 2, 'next' => 'string', ['array'], [[1 => 'string', 'array', 'next' => 'next string']]],
//                <<<JSON
//{
//  0: 1,
//  2: 2,
//  "next": "string",
//  3: ["array"],
//  4: [{
//    1: "string",
//    2: "array",
//    "next": "next string"
//  }]
//}
//JSON
//'{"0":1,"2":2,"next":"string","3":["array"],"4":[{"1":"string","2":"array","next":"next string"}]}' - in Json\Coder
//           ),
            array(
                ['one' => 1, 'tow' => 2],
                <<<JSON
{
  "one": 1,
  "tow": 2
}
JSON
//'{"one":1,"tow":2}' - in Json\Coder
            ),
                //
        );
    }

    /**
     * @dataProvider provider_testCoder
     */
    public function testCoder($in, $jsonString, $out = null)
    {
        $out = isset($out) ? $out : $in; //usialy $out === $in
        $this->assertSame(
                $jsonString, JsonSerializer::jsonSerialize($in)
        );

        $this->assertSame(
                $out, JsonSerializer::jsonUnserialize(JsonSerializer::jsonSerialize($in))
        );
    }

    public function testJsonCoder_ExceptionJsonSerialize()
    {
        $e1 = new Exception('Exception1', 1);
        $e11 = new Exception('Exception11', 11, $e1);
        $this->assertEquals(
                $e11, JsonSerializer::jsonUnserialize(JsonSerializer::jsonSerialize($e11))
        );
    }

// TODO suppoting closures
//    public function not_testJsonCoder_FunJsonSerialize()
//    {
//        $e1 = new PromiseException('Exception1', 1);
//        $message = 'Exception2';
//
//        $fun = function($message) use ($e1) {
//            return new PromiseException($message, 11, $e1);
//        };
//
//        $this->assertEquals(
//                new PromiseException('Exception2', 11, $e1), $fun($message)
//        );
//
//        $afterFun = JsonCoder::jsonUnserialize(JsonCoder::jsonSerialize($fun));
//        $this->assertEquals(
//                $afterFun($message), $fun($message)
//        );
//    }
}
