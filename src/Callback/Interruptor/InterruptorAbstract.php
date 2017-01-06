<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04.01.17
 * Time: 15:55
 */

namespace zaboy\Callback\Interruptor;

use zaboy\Callback\Callback;
use zaboy\Callback\InterruptorInterface;

abstract class InterruptorAbstract extends Callback implements InterruptorInterface
{
    const MACHINE_NAME_KEY = 'service_machine_name';
    const ENV_VAR_MACHINE_NAME = 'MACHINE_NAME';
    const INTERRUPTOR_TYPE_KEY = 'interruptor_type_key';

}
