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

use Fratily\Container\Builder\Exception\LockedException;
use Fratily\Container\Builder\Lazy\LazyNew;

/**
 *
 */
final class Service implements LockableInterface
{
    use LockableTrait;

    /**
     * @var object
     */
    private $value;

    /**
     * @var bool[]
     */
    private $tags    = [];

    /**
     * @var bool[]
     */
    private $aliases = [];

    /**
     * Set the value.
     *
     * @param object $value The value
     *
     * @return $this
     */
    public function setValue(object $value): Service
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set the value by class name.
     *
     * @param string $class The class name
     *
     * @return $this
     */
    public function setValueByClass(string $class): Service
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException();
        }

        return $this->setValue(new LazyNew());
    }

    /**
     * Returns tags.
     *
     * @return string[]
     */
    public function getTags(): array
    {
        return array_keys($this->tags);
    }

    /**
     * Adds tag.
     *
     * @param string $tag The tag name
     *
     * @return $this
     */
    public function addTag(string $tag): Service
    {
        if ($this->isLocked()) {
            throw new LockedException();
        }

        $this->tags[$tag] = true;

        return $this;
    }

    /**
     * Returns aliases.
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        return array_keys($this->aliases);
    }

    /**
     * Adds alias.
     *
     * @param string $alias The alias name
     *
     * @return $this
     */
    public function addAlias(string $alias): Service
    {
        if ($this->isLocked()) {
            throw new LockedException();
        }

        $this->aliases[$alias] = true;

        return $this;
    }
}
