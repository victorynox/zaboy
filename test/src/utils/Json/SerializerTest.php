<?php

namespace zaboy\test\async\Json;

use zaboy\Exception;
use zaboy\utils\Json\Coder;
use zaboy\utils\Json\Serializer as JsonSerializer;

class SerializerTest extends \PHPUnit_Framework_TestCase
{

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
