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
namespace Fratily\Container\Builder;

use Fratily\Container\Builder\Lazy\LazyInterface;
use Fratily\Container\Container;
use Fratily\Container\Builder\LockableTrait;
use Fratily\Container\Builder\Exception\LockedException;

abstract class AbstractValue implements Value\ValueInterface
{
    use LockableTrait;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string
     */
    private $type               = "mixed";

    /**
     * @var bool
     */
    private $typeOverwritable   = true;

    /**
     * @var mixed[]
     */
    private $tags               = [];

    /**
     * @var string[]
     */
    private $aliases            = [];

    /**
     * 値が遅延取得系オブジェクトか確認する
     *
     * @return  bool
     */
    public function isLazy()
    {
        return is_object($this->value) && $this->value instanceof LazyInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function set($value)
    {
        if ($this->isLocked()) {
            throw new LockedException();
        }

        $this->value    = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(string $type, bool $overwritable = false)
    {
        if ($this->isLocked()) {
            throw new LockedException();
        }

        if (!$this->typeOverwritable) {
            throw new \InvalidArgumentException();
        }

        $this->type             = $type;
        $this->typeOverwritable = $overwritable;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return array_keys($this->tags);
    }

    /**
     * {@inheritdoc}
     */
    public function addTag(string $tag)
    {
        if ($this->isLocked()) {
            throw new LockedException();
        }

        if (1 !== preg_match(Container::REGEX_KEY, $tag)) {
            throw new \InvalidArgumentException();
        }

        $this->tags[$tag]   = true;

        return $this;
    }

    /**
     * エイリアスのリストを取得する
     *
     * @return  string[]
     */
    public function getAliases()
    {
        return array_keys($this->aliases);
    }

    /**
     * エイリアスを追加する
     *
     * @param   string  $alias
     *  エイリアス
     *
     * @return  $this
     */
    public function addAlias(string $alias)
    {
        $alias  = ltrim($alias, "\\");

        if (1 !== preg_match(Container::REGEX_KEY, $alias)
            && !class_exists($alias)
            && !interface_exists($alias)
        ) {
            throw new \InvalidArgumentException();
        }

        $this->aliases[$alias]  = true;

        return $this;
    }

    /**
     * エイリアスを削除する
     *
     * @param   string  $alias
     *  エイリアス
     *
     * @return  $this
     */
    public function removeAlias(string $alias)
    {
        if (array_key_exists($alias, $this->aliases)) {
            unset($this->aliases[$alias]);
        }

        return $this;
    }
}
