<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.01.17
 * Time: 17:11
 */

namespace zaboy\test\Callback\Interruptor;

use zaboy\Callback\Interruptor\InterruptorAbstract;
use zaboy\Callback\Interruptor\Multiplexer;
use zaboy\Callback\Interruptor\Process;
use zaboy\Callback\Promiser;
use zaboy\res\Di\InsideConstruct;
use zaboy\test\Callback\CallbackTestDataProvider;

class MultiplexerTest extends CallbackTestDataProvider
{


    /**
     * @param array $interruptors
     * @param $val
     * @dataProvider provider_multiplexerType()
     */
    public function test(array $interruptors, $val){

        $multiplexer = new Multiplexer($interruptors);
        $result = $multiplexer($val);
        $this->assertTrue(isset($result['data']));
        $this->assertEquals(count($interruptors), count($result['data']));
        $this->assertTrue(isset($result[InterruptorAbstract::MACHINE_NAME_KEY]));
        $this->assertEquals(Multiplexer::class, $result[InterruptorAbstract::INTERRUPTOR_TYPE_KEY]);
    }
}
