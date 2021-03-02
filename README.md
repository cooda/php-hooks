# PHP hook system


## Installing PHP-Hooks

The recommended way to install Hook is through
[Composer](https://getcomposer.org/).

```bash
composer require cooda/php-hooks
```


Usage as event/action
-----

```php
<?php

use Cooda\Hooks\HookBus;

$hookBus = new HookBus();

$hookBus->hook('some_hook_name')->add(function ($data) {
    echo $data ." 1\n";
});
$hookBus->hook('some_hook_name')->add(function ($data) {
    echo $data ." 2\n";
});
$hookBus->hook('some_hook_name')->add(function ($data) {
    echo $data ." 3\n";
});

$hookBus->hook('some_hook_name')->do("Hello world");
```
Output
```
Hello world 1
Hello world 2
Hello world 3
```


Usage
-----

```php
<?php

use Cooda\Hooks\HookBus;

$hookBus = new HookBus();

$hookBus->hook('some_hook_name')->add(function (&$arg1, &$arg2) {
    $arg1 += 1;
    $arg2 .= ' world';
});

$hookBus->hook('some_hook_name')->add(function (&$arg1, &$arg2) {
    $arg1 += 1;
    $arg2 .= '!';
});

$hookBus->hook('some_hook_name')->add(function (&$arg1, &$arg2) {
    $arg1 += 1;
    $arg2 = "[".$arg2."]";
});

[$arg1, $arg2] = $hookBus->hook('some_hook_name')->do(1, 'Hello');

echo $arg1 . "\n";
echo $arg2;
```
Output
```
4
[Hello world!]
```


### `Cooda\Hooks\HookBus`
    hook($tag = null): Hook
* ``$tag`` - Hook name.

### `Cooda\Hooks\Hook`
```
// Add callback to hook.

add(callable $callback, float $priority = Hook::DEFAULT_PRIORITY, string $name = null)
```
* ``$callback`` - Callback to be called when the hook is called.
* ``$priority`` - Used to specify the order in which the functions associated with a particular hook are executed (default: 10). Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
* ``$name`` - Callback name.
    
```
// Call hook.

do(...$args)
```
* ``$args`` - Arguments that will be transferred to the callbacks.

```
// Remove callback.

remove($callback, float $priority = Hook::DEFAULT_PRIORITY)
```
* ``$callback``
* ``$priority``

### `Helpers`
```php
<?php

hook('some_hook_name')->add(function () { echo 1; });
hook('some_hook_name')->add(function () { echo 2; });
hook('some_hook_name')->add(function () { echo 3; });
hook('some_hook_name')->do();
/*
Outputs:
123
*/

//chaining
hook('some_hook_name')
    ->add(function () { echo 1; })
    ->add(function () { echo 2; })
    ->add(function () { echo 3; })
    ->do();
/*
Outputs:
123
*/


```
