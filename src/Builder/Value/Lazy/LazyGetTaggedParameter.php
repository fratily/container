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
class LazyGetTaggedParameter extends AbstractLazy
{

    /**
     * @var string|LazyInterface|null
     */
    private $tag;

    /**
     * {@inheritdoc}
     */
    protected static function getDefaultType(): string
    {
        return "array";
    }

    /**
     * {@inheritdoc}
     */
    protected static function getAllowTypes(): ?array
    {
        return ["array"];
    }

    /**
     * {@inheritdoc}
     */
    public function loadValue(Container $container)
    {
        if (null === $this->tag) {
            throw new Exception\SettingIsNotCompletedException();
        }

        return $container->getParameterWithTagged(
            LazyResolver::resolve($container, $this->tag)
        );
    }

    /**
     * タグを設定する
     *
     * @param   string|LazyInterface    $tag
     *  タグ
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function tag($tag)
    {
        if ($this->isLocked()) {
            throw new LockedException();
        }

        if (!is_string($tag)
            && !(static::isLazyObject($tag) && "string" === $tag->getType())
        ) {
            throw new \InvalidArgumentException();
        }

        $this->tag   = $tag;

        return $this;
    }
}
