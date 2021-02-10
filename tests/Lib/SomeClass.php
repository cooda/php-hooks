<?php

namespace Tests\Lib;

use Tests\Lib\SomeHook;
use Cooda\Hooks\Hook;

class SomeClass
{
    public function regularMethod($param1, $param2, $param3)
    {

    }

    public function noParamsMethod()
    {

    }

    public function someHookParamMethod(SomeHook $param, $param1)
    {

    }

    public static function staticMethod(Hook $param1, $param2)
    {

    }
}
