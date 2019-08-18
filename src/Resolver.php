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

use Fratily\Container\Builder\Lazy\LazyGet;
use Fratily\Container\Builder\Lazy\LazyNew;

/**
 *
 */
class Resolver
{
    /**
     * @var Container|null
     */
    private $container;

    /**
     * Returns the container.
     *
     * @return Container
     */
    protected function getContainer(): Container
    {
        if (null === $this->container) {
            throw new \LogicException();
        }

        return $this->container;
    }

    /**
     * Set container.
     *
     * @param Container $container The container
     *
     * @return void
     */
    public function setContainer(Container $container): void
    {
        if (null !== $this->container) {
            throw new \LogicException();
        }

        $this->container = $container;
    }

    /**
     * Returns relation classes.
     *
     * @param string $class The class name
     *
     * @return string[]
     */
    public function getRelationClasses(string $class): array
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException();
        }

        $classes      = [];
        $currentClass = $class;

        do{
            $classes[] = $currentClass;
        }while(false !== ($currentClass = get_parent_class($currentClass)));

        foreach(class_implements($class) as $interface){
            $classes[] = $interface;
        }

        return $classes;
    }

    /**
     * Returns the function arguments.
     *
     * @param \ReflectionFunctionAbstract $reflection The function reflection
     * @param mixed[]                     $positions  The argument values by position
     * @param mixed[]                     $names      The argument values by name
     * @param mixed[]                     $types      The argument values by type
     *
     * @return mixed[]
     */
    public function resolveArguments(
        \ReflectionFunctionAbstract $reflection,
        array $positions,
        array $names,
        array $types
    ): array {
        $arguments = [];

        foreach ($reflection->getParameters() as $parameter) {
            $arguments[] = $this->resolveParameter($parameter, $positions, $names, $types);
        }

        return $arguments;
    }

    /**
     * Resolve argument value.
     *
     * @param \ReflectionParameter $parameter The parameter reflection
     * @param mixed[]              $positions The argument values by position
     * @param mixed[]              $names     The argument values by name
     * @param mixed[]              $types     The argument values by type
     *
     * @return mixed
     */
    public function resolveParameter(
        \ReflectionParameter $parameter,
        array $positions,
        array $names,
        array $types
    ) {
        if (array_key_exists($parameter->getPosition(), $positions)) {
            return $positions[$parameter->getPosition()];
        }

        if (array_key_exists($parameter->getName(), $names)) {
            return $names[$parameter->getName()];
        }

        $class  = null;

        if ($parameter->hasType() && !$parameter->getType()->isBuiltin()) {
            try {
                $class  = $parameter->getClass();
            } catch (\ReflectionException $e) {
                // This ReflectionException is thrown if the argument type declaration is invalid.
                throw new Exception\InvalidParameterDefinedException(
                    "Type specification of {$this->getParameterInfoText($parameter)}"
                        . " is invalid.",
                    0,
                    $e
                );
            }

            if (array_key_exists($class->getName(), $types)) {
                return $types[$class->getName()];
            }

            if ($this->getContainer()->has($class->getName())) {
                return new LazyGet($class->getName());
            }
        }

        if ($parameter->isDefaultValueAvailable()) {
            try {
                return $parameter->getDefaultValue();
            } catch (\ReflectionException $e) {
                throw new \LogicException($e->getMessage(), $e->getCode(), $e);
            }
        }

        if (!$parameter->allowsNull() && null !== $class && $class->isInstantiable()) {
            return new LazyNew($class->getName());
        }

        return null;
    }

    /**
     * Returns parameter debug info text.
     *
     * @param \ReflectionParameter $parameter The parameter reflection
     *
     * @return string
     */
    public function getParameterInfoText(\ReflectionParameter $parameter): string
    {
        $func   = $parameter->getDeclaringFunction();
        $posStr = str_repeat("\$.., ", $parameter->getPosition());
        $target = $func instanceof \ReflectionMethod
            ? ($func->getDeclaringClass()->getName() . "::" . $func->getName())
            : $func->getName()
        ;

        return $target . "(" . $posStr . "\$" . $parameter->getName() . ")";
    }
}
