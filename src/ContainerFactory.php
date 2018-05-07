<?php
/**
 * FratilyPHP Container
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento.oka@kentoka.com>
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

        return new Container(new Resolver\Resolver($this->reflector), $auto);
    }

    /**
     *
     *
     * @param   string[]    $classes
     * @param   bool    $auto
     *
     * @return  Container
     */
    public function createWithConfig(array $classes, bool $auto = false){
        $container  = $this->create($auto);

        foreach($classes as $class){

        }

        $container->lock();

        return $container;
    }
}