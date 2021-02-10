<?php


namespace Cooda\Hook;



class HookBus
{
    /**
     * @var array
     */
    protected $hooks = [];

    /**
     * @var static
     */
    protected static $instance;

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if (! static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * @param string $tag
     * @return Hook
     */
    public function hook(?string $tag = null): Hook
    {
        if (is_null($tag)) {
            return new Hook();
        }

        if (! $this->has($tag)) {
            $this->hooks[$tag] = new Hook($tag);
        }

        return $this->get($tag);
    }

    /**
     * @param Hook $hook
     */
    public function add(Hook $hook)
    {
        $this->hooks[$hook->getTag()] = $hook;
    }

    /**
     * @param string $tag
     * @return Hook|false
     */
    public function get(string $tag): ?Hook
    {
        if ($this->has($tag)) {
            return $this->hooks[$tag];
        }

        return null;
    }

    /**
     * @param string $tag
     * @return bool
     */
    public function has(string $tag)
    {
        return isset($this->hooks[$tag]);
    }
}
