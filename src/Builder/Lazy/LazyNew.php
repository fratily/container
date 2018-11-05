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
     * @var mixed[]
     */
    private $parameters;

    /**
     * @var mixed[]
     */
    private $types;

    /**
     * Constructor
     *
     * @param   string  $class
     *  クラス名
     * @param   mixed[] $parameters
     *  追加指定パラメータの配列
     * @param   mixed[] $types
     *  追加指定型指定解決値の配列
     */
    public function __construct(
        string $class,
        array $parameters = [],
        array $types = []
    ){
        if(!class_exists($class)){
            throw new \InvalidArgumentException();
        }

        $this->class        = $class;
        $this->parameters   = $parameters;
        $this->types        = $types;
    }

    /**
     * @inheritdoc
     *
     * @return  object
     */
    public function load(\Fratily\Container\Container $container){
        return $container
            ->getResolver()
            ->getClassResolver($this->class)
            ->getInstanceGenerator()
            ->generate($container, $this->parameters, $this->types)
        ;
    }
}
