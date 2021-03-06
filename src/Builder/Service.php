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
use Fratily\Container\Builder\Lazy\LazyInterface;
use Fratily\Container\Builder\Lazy\LazyInvoke;
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
    private $tags = [];

    /**
     * Returns the value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

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
     * @param string|LazyInterface $class The class name
     *
     * @return $this
     */
    public function setValueByClass($class): Service
    {
        if (
            !(is_string($class) && class_exists($class))
            && !(is_object($class) && $class instanceof LazyInterface)
        ) {
            throw new \InvalidArgumentException();
        }

        return $this->setValue(new LazyNew($class));
    }

    /**
     * Set the value by factory callback.
     *
     * @param callable|LazyInterface $factory The factory
     *
     * @return $this
     */
    public function setValueByFactory($factory): Service
    {
        if (
            !(is_callable($factory))
            && !(is_object($factory) && $factory instanceof LazyInterface)
        ) {
            throw new \InvalidArgumentException();
        }

        return $this->setValue(new LazyInvoke($factory));
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
}
