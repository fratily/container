<?php
/**
 * FratilyPHP Container
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento-oka@kentoka.com>
 * @copyright   (c) Kento Oka
 * @license     MIT
 * @since       1.0.0
 */
namespace Fratily\Container\Builder\Lazy;

use Fratily\Container\Builder\LockableTrait;
use Fratily\Container\Container;
use Fratily\Container\Builder\Exception\LockedException;

/**
 *
 */
class LazyInvoke implements LazyInterface
{
    use LockableTrait;

    /**
     * @var callable|LazyInterface
     */
    private $invokeFunction;

    /**
     * @var mixed[]
     */
    private $arguments;

    /**
     * Constructor.
     *
     * @param callable|LazyInterface $invokeFunction The invoke callback
     * @param mixed[]                $arguments      The invoke callback's arguments
     */
    public function __construct($invokeFunction, array $arguments = [])
    {
        if (
            !is_callable($invokeFunction)
            || !(is_object($invokeFunction) && $invokeFunction instanceof LazyInterface)
        ) {
            throw new \InvalidArgumentException();
        }

        $this->invokeFunction = $invokeFunction;
        $this->arguments      = $arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function load(Container $container)
    {
        $this->lock();

        return call_user_func_array(
            (is_object($this->invokeFunction) && $this->invokeFunction instanceof LazyInterface)
                ? $this->invokeFunction->load($container)
                : $this->invokeFunction,
            array_map(
                function ($value) use ($container) {
                    return (is_object($value) && $value instanceof LazyInterface)
                        ? $value->load($container)
                        : $value
                    ;
                },
                $this->arguments
            )
        );
    }
}
