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
class Injection implements LockableInterface
{
    use LockableTrait;

    public const POSITION = "position";
    public const NAME     = "name";
    public const TYPE     = "type";

    /**
     * @var array[]
     */
    private $arguments = [
        self::POSITION => [],
        self::NAME     => [],
        self::TYPE     => [],
    ];

    /**
     * @var array[]
     */
    private $setters   = [];

    /**
     * Returns arguments.
     *
     * @param string $type The arguments injection type
     *
     * @return mixed[]
     */
    public function getArguments(string $type): array
    {
        if (!isset($this->arguments[$type])) {
            throw new \InvalidArgumentException();
        }

        return $this->arguments[$type];
    }

    /**
     * Set arguments.
     *
     * @param mixed[] $arguments The arguments
     *
     * @return $this
     */
    public function setArguments(array $arguments): Injection
    {
        if ($this->isLocked()) {
            throw new LockedException();
        }

        $arguments = array_values($arguments);
        array_unshift($arguments, "dummy");
        unset($arguments[0]);

        $this->arguments[self::POSITION] = array_values($arguments);

        return $this;
    }

    /**
     * Adds argument.
     *
     * @param int|string $key   The argument key
     * @param mixed      $value The value
     *
     * @return $this
     */
    public function addArgument($key, $value): Injection
    {
        if ($this->isLocked()) {
            throw new LockedException();
        }

        if (!is_int($key) && !is_string($key)) {
            throw new \InvalidArgumentException();
        }

        if (is_int($key)) {
            $this->arguments[self::POSITION][$key] = $value;
        } elseif (class_exists($key) || interface_exists($key)) {
            $this->arguments[self::TYPE][$key] = $value;
        } else {
            $this->arguments[self::NAME][$key] = $value;
        }

        return $this;
    }

    /**
     * Returns setters arguments.
     *
     * @return array[]
     */
    public function getSetters(): array
    {
        return $this->setters;
    }

    /**
     * Add setter.
     *
     * @param string $method The setter method name
     * @param array  $args   The setter arguments
     *
     * @return $this
     */
    public function addSetter(string $method, array $args): Injection
    {
        if ($this->isLocked()) {
            throw new LockedException();
        }

        $this->setters[$method] = $args;

        return $this;
    }
}
