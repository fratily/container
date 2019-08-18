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
namespace Fratily\Container;

use Fratily\Container\Builder\Injection;
use Fratily\Container\Builder\Lazy\LazyArray;
use Fratily\Container\Builder\Lazy\LazyInterface;
use Fratily\Reflection\ReflectionCallable;
use Psr\Container\ContainerInterface;

/**
 *
 */
class Container implements ContainerInterface
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     *@var object[]
     */
    private $services   = [];

    /**
     * @var mixed[]
     */
    private $parameters = [];

    /**
     * Constructor.
     *
     * @param Repository $repository The repository
     * @param Resolver   $resolver   The resolver
     */
    public function __construct(Repository $repository, Resolver $resolver) {
        $this->repository = $repository;
        $this->resolver   = $resolver;
    }

    /**
     * Returns the repository.
     *
     * @return Repository
     */
    public function getRepository(): Repository
    {
        return $this->repository;
    }

    /**
     * Returns the resolver.
     *
     * @return Resolver
     */
    public function getResolver(): Resolver
    {
        return $this->resolver;
    }

    /**
     * Return new instance.
     *
     * @param string         $class               The class name
     * @param Injection|null $additionalInjection The additional Injection
     *
     * @return object
     */
    public function new(string $class, Injection $additionalInjection = null)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException();
        }

        $reflection = null;

        try {
            $reflection = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            throw new \LogicException("", 0, $e);
        }

        if (!$reflection->isInstantiable()) {
            throw new \InvalidArgumentException();
        }

        $instance   = null;
        $injections = $this->getRepository()->getInjectionsByClasses(
            $this->getResolver()->getRelationClasses($class)
        );

        if (null !== $additionalInjection) {
            array_unshift($injections, $additionalInjection);
        }

        if (null !== $reflection->getConstructor()) {
            $positions = [];
            $names     = [];
            $types     = [];

            $classInjection = $this->getRepository()->hasInjection($class)
                ? $this->getRepository()->getInjection($class)
                : null
            ;

            foreach ($injections as $injection) {
                if ($additionalInjection === $injection || $classInjection === $injection) {
                    $positions += $injection->getArguments(Injection::POSITION);
                }

                $names += $injection->getArguments(Injection::NAME);
                $types += $injection->getArguments(Injection::TYPE);
            }

            $arguments = new LazyArray(
                $this->getResolver()->resolveArguments(
                    $reflection->getConstructor(),
                    $positions,
                    $names,
                    $types
                )
            );

            $instance = $reflection->newInstanceArgs($arguments->load($this));
        } else {
            $instance = $reflection->newInstance();
        }

        $calledSetters = [];

        foreach ($injections as $injection) {
            foreach ($injection->getSetters() as $method => $args) {
                if (!isset($calledSetters[$method])) {
                    call_user_func_array(
                        [$instance, $method],
                        (new LazyArray($args))->load($this)
                    );
                }

                $calledSetters[$method] = ($calledSetters[$method] ?? 0) + 1;
            }
        }

        return $instance;
    }

    /**
     * Execute callback and returns that returns.
     *
     * @param callable $callback  The callback
     * @param mixed[]  $positions The argument values by position
     * @param mixed[]  $names     The argument values by name
     * @param mixed[]  $types     The argument values by type
     *
     * @return mixed
     */
    public function invoke(callable $callback, array $positions, array $names, array $types) {
        return call_user_func_array(
            $callback,
            $this->getResolver()->resolveArguments(
                (new ReflectionCallable($callback))->getReflection(),
                $positions,
                $names,
                $types
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException();
        }

        if (!isset($this->services[$id])) {
            if (!$this->has($id)) {
                throw new Exception\ServiceNotFoundException();
            }

            $service = $this->getRepository()->getService($id)->getValue();

            if (is_object($service)) {
                if ($service instanceof LazyInterface) {
                    $service = $service->load($this);
                }
            }

            if (!is_object($service)) {
                throw new \LogicException();
            }

            $this->services[$id] = $service;
        }

        return $this->services[$id];
    }

    /**
     * Returns services by tag.
     *
     * @param string $tag The tag
     *
     * @return object[]
     */
    public function getByTag(string $tag): array
    {
        $services = [];

        foreach ($this->getRepository()->getServiceIdListByTag($tag) as $id) {
            $services[] = $this->get($id);
        }

        return $services;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  \InvalidArgumentException
     */
    public function has($id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException();
        }

        return $this->getRepository()->hasService($id);
    }

    /**
     * Returns parameter by id.
     *
     * @param string $id The parameter id
     *
     * @return mixed
     */
    public function getParameter(string $id)
    {
        if (!isset($this->parameters[$id])) {
            if (!$this->hasParameter($id)) {
                throw new Exception\ParameterNotFoundException();
            }

            $parameter = $this->getRepository()->getParameter($id)->getValue();

            if (is_object($parameter) && $parameter instanceof LazyInterface) {
                $parameter = $parameter->load($this);
            }

            $this->parameters[$id] = $parameter;
        }

        return $this->parameters[$id];
    }

    /**
     * Returns parameters by tag.
     *
     * @param string $tag The tag
     *
     * @return mixed[]
     */
    public function getParametersByTag(string $tag): array
    {
        $parameters = [];

        foreach ($this->getRepository()->getParameterIdListByTag($tag) as $id) {
            $parameters[] = $this->get($id);
        }

        return $parameters;
    }

    /**
     * Returns true if parameter is defined.
     *
     * @param string $id The parameter id
     *
     * @return bool
     */
    public function hasParameter(string $id): bool
    {
        return $this->getRepository()->hasParameter($id);
    }
}
