<?php


use Cooda\Hook\Helpers\Util;
use PHPUnit\Framework\TestCase;
use Tests\Lib\SomeClass;

class UtilTest extends TestCase
{
    public function testGetCallableName()
    {
        $closures = function () {};
        $obj = new SomeClass();

        $this->assertEquals(Util::getCallableName($closures), spl_object_hash($closures));
        $this->assertEquals(Util::getCallableName('array_merge'), 'array_merge');
        $this->assertEquals(Util::getCallableName([$obj, 'regularMethod']), SomeClass::class.'::regularMethod');
        $this->assertEquals(Util::getCallableName([$obj, 'staticMethod']), SomeClass::class.'::staticMethod');
        $this->assertEquals(Util::getCallableName([SomeClass::class, 'staticMethod']), SomeClass::class.'::staticMethod');
        $this->assertEquals(Util::getCallableName(['Tests\Lib\SomeClass', 'staticMethod']), SomeClass::class.'::staticMethod');
        $this->assertEquals(Util::getCallableName('Tests\Lib\SomeClass::staticMethod'), SomeClass::class.'::staticMethod');
    }

    public function testHasHookHint()
    {
        $obj = new SomeClass();

        $this->assertTrue(Util::hasHookHint([$obj, 'someHookParamMethod']));
        $this->assertFalse(Util::hasHookHint([$obj, 'regularMethod']));
    }

    public function testHasHookHintParameter()
    {
        $obj = new SomeClass();

        $this->assertTrue(Util::hasHookHintParameter(
            Util::getFunctionParameters([$obj, 'someHookParamMethod'])
        ));

        $this->assertTrue(Util::hasHookHintParameter(
            Util::getFunctionParameters(['Tests\Lib\SomeClass', 'staticMethod'])
        ));

        $this->assertTrue(Util::hasHookHintParameter(
            Util::getFunctionParameters([SomeClass::class, 'staticMethod'])
        ));

        $this->assertFalse(Util::hasHookHintParameter(
            Util::getFunctionParameters([$obj, 'regularMethod'])
        ));

        $this->assertFalse(Util::hasHookHintParameter(
            Util::getFunctionParameters([$obj, 'noParamsMethod'])
        ));
    }

    public function testGetFunctionParameters()
    {
        $this->assertIsArray(Util::getFunctionParameters(function ($q, $w){}), ReflectionFunctionAbstract::class);
        $this->assertCount(2, Util::getFunctionParameters([SomeClass::class, 'staticMethod']));
    }

    public function testGetReflectionFunction()
    {
        $obj = new SomeClass();

        $this->assertInstanceOf(ReflectionFunctionAbstract::class, Util::getReflectionFunction(function (){}));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class, Util::getReflectionFunction('array_merge'));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class, Util::getReflectionFunction([$obj, 'regularMethod']));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class, Util::getReflectionFunction([$obj, 'staticMethod']));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class, Util::getReflectionFunction([SomeClass::class, 'staticMethod']));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class, Util::getReflectionFunction(['Tests\Lib\SomeClass', 'staticMethod']));
        $this->assertInstanceOf(ReflectionFunctionAbstract::class, Util::getReflectionFunction('Tests\Lib\SomeClass::staticMethod'));
    }
}
