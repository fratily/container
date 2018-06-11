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

use Fratily\Reflection\Reflector\ClassReflector;

/**
 *
 */
class ContainerFactory{

    /**
     * @var ClassReflector
     */
    private $reflector;

    /**
     * Constructor
     *
     * @param   ClassReflector  $reflector
     */
    public function __construct(ClassReflector $reflector = null){
        if($reflector !== null){
            $this->reflector    = $reflector;
        }
    }

    /**
     *
     *
     * @param   bool    $auto
     *
     * @return  Container
     */
    public function create(bool $auto = false){
        if($this->reflector === null){
            $this->reflector    = new ClassReflector();
        }

        return new Container(
            new Injection\Factory(
                new Resolver\Resolver($this->reflector, $auto)
            )
        );
    }

    /**
     *
     *
     * @param   string[]    $classes
     * @param   bool    $auto
     *
     * @return  Container
     */
    public function createWithConfig(array $classes, bool $auto = false, callable $modify = null){
        $container  = $this->create($auto);

        $configs    = [];

        foreach($classes as $class){
            if(is_string($class)){
                if(!class_exists($class)){
                    throw new \InvalidArgumentException();
                }

                $config = new $class();
            }else{
                $config = $class;
            }

            if(!(is_object($config) && $config instanceof ContainerConfigInterface)){
                throw new \InvalidArgumentException();
            }

            $config->define($container);

            $configs[]  = $config;
        }

        $container->lock();

        foreach($configs as $config){
            $config->modify($container);
        }

        if($modify !== null){
            $modify($container);
        }

        return $container;
    }
}