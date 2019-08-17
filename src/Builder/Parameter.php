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

/**
 *
 */
final class Parameter implements LockableInterface
{
    use LockableTrait;

    /**
     * @var mixed
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
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value): Parameter
    {
        $this->value = $value;

        return $this;
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
    public function addTag(string $tag): Parameter
    {
        if ($this->isLocked()) {
            throw new LockedException();
        }

        $this->tags[$tag] = true;

        return $this;
    }
}
