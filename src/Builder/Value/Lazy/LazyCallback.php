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
namespace Fratily\Container\Builder\Value\Lazy;

use Fratily\Container\Container;
use Fratily\Container\Builder\Exception\LockedException;

/**
 *
 */
class LazyCallback extends AbstractLazy
{

    /**
     * @var callable|LazyInterface|null
     */
    private $callback;

    /**
     * @var array|LazyInterface
     */
    private $args   = [];

    /**
     * {@inheritdoc}
     */
    protected static function getDefaultType(): string
    {
        return "callable";
    }

    /**
     * {@inheritdoc}
     */
    protected static function getAllowTypes(): ?array
    {
        return ["callable"];
    }

    /**
     * {@inheritdoc}
     */
    public function loadValue(Container $container)
    {
        if (null === $this->callback) {
            throw new Exception\SettingIsNotCompletedException();
        }

        return call_user_func_array(
            LazyResolver::resolve($container, $this->callback),
            LazyResolver::resolveArray($container, $this->args)
        );
    }

    /**
     * 実行するコールバックを設定する
     *
     * @param   callable|LazyInterface  $callback
     *  実行するコールバック
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function callback($callback)
    {
        if ($this->isLocked()) {
            throw new LockedException();
        }

        if (!is_callable($callback)
            && !(
                static::isLazyObject($callback)
                && "callable" === $callback->getType()
            )
        ) {
            throw new \InvalidArgumentException();
        }

        $this->callback = $callback;

        return $this;
    }
    /**
     * 引数を設定する
     *
     * @param   array|LazyInterface $args
     *  引数
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function args($args)
    {
        if ($this->isLocked()) {
            throw new LockedException();
        }

        if (!is_array($args)
            && !(
                $this->isLazyObject($args)
                && "array" === $args->getType()
            )
        ) {
            throw new \InvalidArgumentException();
        }

        $this->args = $args;

        return $this;
    }
}
