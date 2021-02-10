<?php


namespace Cooda\Hook;


use Cooda\Hook\Helpers\Util;

class Hook implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * Default priority.
     */
    public const DEFAULT_PRIORITY = 10;

    /**
     * Delimiter between priority and callback name.
     */
    public const PND = '|';

    /**
     * @var bool
     */
    protected $changed = false;

    /**
     * Determinate stopped execution callbacks.
     *
     * @var bool
     */
    protected $break = false;

    /**
     * Callbacks list.
     *
     * @var array
     */
    protected $callbacks = [];

    /**
     * @var string
     */
    protected $tag;

    /**
     * @var Closure
     */
    private $afterExec;

    /**
     * @var Closure
     */
    private $beforeExec;

    /**
     * Hook constructor.
     *
     * @param string $tag hook name
     * @param array $callbacks
     */
    public function __construct(string $tag = null, array $callbacks = [])
    {
        $this->tag       = $tag;
        $this->callbacks = $callbacks;
    }

    /**
     * @param callable $callback
     * @param float|null $priority
     * @param string|null $name
     * @return $this
     */
    public function add(callable $callback, float $priority = self::DEFAULT_PRIORITY, string $name = null)
    {
        $key = self::createKey($name ?? $callback, $priority);

        $this->callbacks[$key] = [
            'callback' => $callback,
            'needed_hook' => Util::hasHookHint($callback)
        ];

        $this->changed = true;

        return $this;
    }

    /**
     * @param \Closure $callback
     */
    public function before(\Closure $callback)
    {
        $this->beforeExec = $callback->bindTo($this);
    }

    /**
     * @param \Closure $callback
     */
    public function after(\Closure $callback)
    {
        $this->afterExec = $callback->bindTo($this);
    }

    /**
     * @param mixed ...$args
     * @return null
     */
    public function do(...$args)
    {
        return $this->exec(...$args);
    }

    /**
     * @param array $args
     * @return null
     */
    protected function exec(&...$args)
    {
        $this->sortCallbacks();

        $this->break = false;
        // Call before execution callbacks
        if ($this->beforeExec) {
            ($this->beforeExec)(...$args);
        }

        foreach ($this->getCallbacks() as $key => $callback) {

            if ($this->break) {
                $this->break = false;
                break;
            }

            if ($callback['needed_hook']) {
                $res = $callback['callback']($this, ...$args);
            } else {
                $res = $callback['callback'](...$args);
            }

            if (!$args && is_array($res)) {
                $args = $res;
            }
        }

        // Call after execution callbacks
        if ($this->afterExec) {
            ($this->afterExec)(...$args);
        }

        return $args;
    }

    /**
     * @param $callback
     * @param float|int $priority
     * @return $this
     */
    public function remove($callback, float $priority = self::DEFAULT_PRIORITY)
    {
        unset($this->callbacks[self::createKey($callback, $priority)]);

        return $this;
    }

    /**
     * @param null $callback
     * @param float|int $priority
     * @return bool
     */
    public function has($callback = null, float $priority = self::DEFAULT_PRIORITY)
    {
        if (! $callback) {
            return (bool) $this->count();
        }

        return isset($this->callbacks[self::createKey($callback, $priority)]);
    }

    /**
     * Sort callbacks by priority
     */
    public function sortCallbacks()
    {
        if ($this->changed) {
            ksort($this->callbacks, SORT_NUMERIC);
        }

        $this->changed = false;
    }

    /**
     * Stop execution hooks.
     *
     * @return $this
     */
    public function break()
    {
        $this->break = true;

        return $this;
    }

    /**
     * Get tag.
     *
     * @return string|null
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Get all callbacks.
     *
     * @return array
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }

    /**
     * @param float|int $priority
     * @param callable|string $callbackName
     * @return string
     */
    public static function createKey($callbackName, float $priority = self::DEFAULT_PRIORITY): string
    {
        return "$priority" . self::PND . Util::getCallableName($callbackName);
    }

    /** ==================================================================================================
     * ArrayAccess
     * ===================================================================================================
     */
    /**
     * Determines whether an offset value exists.
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->callbacks[$offset]);
    }

    /**
     * Retrieves a value at a specified offset.
     *
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->callbacks[$offset]) ? $this->callbacks[$offset] : null;
    }

    /**
     * Sets a value at a specified offset.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset) ) {
            $this->callbacks[] = $value;
        } else {
            $this->callbacks[$offset] = $value;
        }
    }

    /**
     * Unset a specified offset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->callbacks[$offset]);
    }

    /**
     * Returns the current element.
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->callbacks);
    }

    /**
     * Moves forward to the next element.
     *
     * @return mixed|void
     */
    public function next()
    {
        return next($this->callbacks);
    }

    /**
     * Returns the key of the current element.
     *
     * @return bool|float|int|string|null
     */
    public function key()
    {
        return key($this->callbacks);
    }

    /**
     * Checks if current position is valid.
     *
     * @return bool
     */
    public function valid()
    {
        return key($this->callbacks) !== null;
    }

    /**
     * Rewinds the Iterator to the first element.
     */
    public function rewind()
    {
        reset($this->callbacks);
    }

    /**
     * Get items count.
     *
     * @return int
     */
    public function count()
    {
        return count($this->callbacks);
    }
}
