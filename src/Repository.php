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

use Fratily\Container\Builder\ContainerBuilder;
use Fratily\Container\Builder\Injection;
use Fratily\Container\Builder\Parameter;
use Fratily\Container\Builder\Service;

/**
 *
 */
class Repository
{
    /**
     * @var ContainerBuilder
     */
    private $builder;

    /**
     * @var string[][]
     */
    private $parameterIdListByTag = [];

    /**
     * @var string[][]
     */
    private $serviceIdListByTag   = [];

    /**
     * Constructor
     *
     * @param ContainerBuilder $builder The container builder
     */
    public function __construct(ContainerBuilder $builder)
    {
        if (!$builder->isLocked()) {
            throw new \InvalidArgumentException();
        }

        $this->builder = $builder;

        foreach ($this->builder->getParameters() as $id => $parameter) {
            foreach ($parameter->getTags() as $tag) {
                if (!isset($this->parameterIdListByTag[$tag])) {
                    $this->parameterIdListByTag[$tag] = [];
                }

                $this->parameterIdListByTag[$tag][] = $id;
            }
        }

        foreach ($this->builder->getServices() as $id => $service) {
            foreach ($service->getTags() as $tag) {
                if (!isset($this->serviceIdListByTag[$tag])) {
                    $this->serviceIdListByTag[$tag] = [];
                }

                $this->serviceIdListByTag[$tag][] = $id;
            }
        }
    }

    /**
     * Returns the parameters.
     *
     * @return Parameter[]
     */
    public function getParameters(): array
    {
        return $this->builder->getParameters();
    }

    /**
     * Returns the parameter.
     *
     * @param string $id The parameter id
     *
     * @return Parameter
     */
    public function getParameter(string $id): Parameter
    {
        if (!$this->hasParameter($id)) {
            throw new \LogicException();
        }

        return $this->getParameters()[$id];
    }

    /**
     * Returns the tagged parameter id list.
     *
     * @param string $tag The tag name
     *
     * @return string[]
     */
    public function getParameterIdListByTag(string $tag): array
    {
        return isset($this->parameterIdListByTag[$tag])
            ? $this->parameterIdListByTag[$tag]
            : []
        ;
    }

    /**
     * Return true if parameter is defined.
     *
     * @param string $id The parameter id
     *
     * @return bool
     */
    public function hasParameter(string $id): bool
    {
        return isset($this->getParameters()[$id]);
    }


    /**
     * Returns the services.
     *
     * @return Service[]
     */
    public function getServices(): array
    {
        return $this->builder->getServices();
    }

    /**
     * Returns the service.
     *
     * @param string $id The service id
     *
     * @return Service
     */
    public function getService(string $id): Service
    {
        if (!$this->hasService($id)) {
            throw new \LogicException();
        }

        return $this->getServices()[$id];
    }

    /**
     * Returns the tagged service id list.
     *
     * @param string $tag The tag name
     *
     * @return string[]
     */
    public function getServiceIdListByTag(string $tag): array
    {
        return isset($this->serviceIdListByTag[$tag])
            ? $this->serviceIdListByTag[$tag]
            : []
        ;
    }

    /**
     * Return true if service is defined.
     *
     * @param string $id The service id
     *
     * @return bool
     */
    public function hasService(string $id): bool
    {
        return isset($this->getServices()[$id]);
    }

    /**
     * Returns the Injections.
     *
     * @return Injection[]
     */
    public function getInjections(): array
    {
        return $this->builder->getInjections();
    }

    /**
     * Returns the Injections by classes.
     *
     * @param string[] $classes The classes
     *
     * @return Injection[]
     */
    public function getInjectionsByClasses(array $classes): array
    {
        $injections = [];

        foreach ($classes as $class) {
            if (!is_string($class)) {
                throw new \InvalidArgumentException();
            }

            if ($this->hasInjection($class)) {
                $injections[] = $this->getInjection($class);
            }
        }

        return $injections;
    }

    /**
     * Returns the Injection.
     *
     * @param string $class The class name
     *
     * @return Injection
     */
    public function getInjection(string $class): Injection
    {
        if (!$this->hasInjection($class)) {
            throw new \LogicException();
        }

        return $this->getInjections()[$class];
    }

    /**
     * Return true if Injection is defined.
     *
     * @param string $class The class name
     *
     * @return bool
     */
    public function hasInjection(string $class): bool
    {
        return isset($this->getInjections()[$class]);
    }
}
