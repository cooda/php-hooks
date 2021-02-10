<?php


use Cooda\Hooks\HookBus;

if (! function_exists('hook')) {
    /**
     * Return default Hook.
     *
     * @param array $args
     * @return \Cooda\Hooks\Hook
     */
    function hook(...$args)
    {
        return HookBus::getInstance()->hook(...$args);
    }
}

if (! function_exists('hookBus')) {
    /**
     * Return default HookBus.
     *
     * @return HookBus
     */
    function hookBus()
    {
        return HookBus::getInstance();
    }
}
