<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03.01.17
 * Time: 13:18
 */

namespace zaboy\Callback;

interface InterruptorInterface
{
    public function __invoke($value);
}
