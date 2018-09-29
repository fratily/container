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
namespace Fratily\Container\Builder\Lazy;

/**
 *
 */
class LazyNew implements LazyInterface{

    /**
     * @var string
     */
    private $class;

    /**
     * Constructor
     *
     * @param   string  $class
     *  クラス名
     */
    public function __construct(string $class){
        if(!is_class($class)){
            throw new \InvalidArgumentException();
        }
        $this->class    = $class;
    }

    /**
     * @inheritdoc
     *
     * @return  object
     */
    public function load(\Fratily\Container\Container $container){
        return $container
            ->getResolver()
            ->getClassResolver($class)
            ->getInstanceGenerator()
            ->generate()
        ;
    }
}
