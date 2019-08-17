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
class ContainerBuilder implements LockableInterface
{
    /**
     * @var bool
     */
    private $locked     = false;

    /**
     * @var Injection[]
     */
    private $injections = [];

    /**
     * @var Parameter[]
     */
    private $parameters = [];

    /**
     * @var Service[]
     */
    private $services   = [];

    /**
     * {@inheritdoc}
     */
    public function lock(): void
    {
        if (!$this->locked) {
            $this->locked = true;

            $conflict = array_intersect_key($this->services, $this->parameters);

            if (0 !== count($conflict)) {
                throw new \LogicException();
            }

            foreach ($this->injections as $injection) {
                $injection->lock();
            }

            foreach ($this->parameters as $parameter) {
                $parameter->lock();
            }

            foreach ($this->services as $service) {
                $service->lock();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * Returns the Injection by class name.
     *
     * @return Injection[]
     */
    public function getInjections(): array
    {
        return $this->injections;
    }

    /**
     * Returns the Injection.
     *
     * If the instance is undefined, return the newly generated one.
     *
     * @param string $class The class name
     *
     * @return Injection
     */
    public function injection(string $class): Injection
    {
        if ("\\" === substr($class, 0, 1)) {
            throw new \InvalidArgumentException();
        }

        if (!class_exists($class) && !interface_exists($class)) {
            throw new \InvalidArgumentException();
        }

        if ($this->isLocked()) {
            throw new LockedException();
        }

        if (!isset($this->injections[$class])) {
            $this->injections[$class] = new Injection();
        }

        return $this->injections[$class];
    }

    /**
     * Returns the parameters by id.
     *
     * @return Parameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Returns the Parameter.
     *
     * If the instance is undefined, return the newly generated one.
     *
     * @param string $id The id
     *
     * @return Parameter
     */
    public function parameter(string $id): Parameter
    {
        if (isset($this->services[$id])) {
            throw new \InvalidArgumentException();
        }

        if ($this->isLocked()) {
            throw new LockedException();
        }

        if (!isset($this->parameters[$id])) {
            $this->parameters[$id] = new Parameter();
        }

        return $this->parameters[$id];
    }

    /**
     * Returns the services by id.
     *
     * @return Service[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * Returns the Service.
     *
     * If the instance is undefined, return the newly generated one.
     *
     * @param string $id The id
     *
     * @return Service
     */
    public function service(string $id): Service
    {
        if (isset($this->parameters[$id])) {
            throw new \InvalidArgumentException();
        }

        if ($this->isLocked()) {
            throw new LockedException();
        }

        if (!isset($this->services[$id])) {
            $this->services[$id] = new Service();
        }

        return $this->services[$id];
    }
}
