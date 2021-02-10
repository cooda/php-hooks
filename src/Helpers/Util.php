<?php


namespace Cooda\Hook\Helpers;


use Cooda\Hook\Hook;

class Util
{
    /**
     * Create callback name from callable
     *
     * @param callable $callback
     * @return string|null
     */
    public static function getCallableName($callback): ?string
    {
        if (is_string($callback)) {
            return trim($callback);
        }

        if ($callback instanceof \Closure) {
            return spl_object_hash($callback);
        }

        if (is_array($callback)) {
            [$class, $method] = $callback;

            return trim(is_object($class) ? get_class($class) : $class) . "::" . trim($method);
        }

        return null;
    }

    /**
     * @param array $parameters
     * @return bool
     */
    public static function hasHookHintParameter(array $parameters): bool
    {
        if ($parameters && $type = $parameters[0]->getType()) {
            return is_a($type->getName(), Hook::class, true);
        }

        return false;
    }

    /**
     * @param callable $function
     * @return bool
     * @throws \ReflectionException
     */
    public static function hasHookHint(callable $function)
    {
        return self::hasHookHintParameter(self::getFunctionParameters($function));
    }

    /**
     * @param callable $function
     * @return \ReflectionParameter[]
     * @throws \ReflectionException
     */
    public static function getFunctionParameters(callable $function)
    {
        return self::getReflectionFunction($function)->getParameters();
    }

    /**
     * Returns a reflection object for any callable.
     *
     * @param callable $function
     *
     * @return \ReflectionFunctionAbstract
     * @throws \ReflectionException
     */
    public static function getReflectionFunction(callable $function)
    {
        if (is_string($function) && strpos($function, '::')) {
            $function = explode('::', $function);
        }

        if (is_array($function)) {
            return new \ReflectionMethod($function[0], $function[1]);
        }

        if (is_object($function) && ! $function instanceof \Closure) {
            return new \ReflectionMethod($function, '__invoke');
        }

        return new \ReflectionFunction($function);
    }
}
