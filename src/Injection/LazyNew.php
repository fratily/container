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
namespace Fratily\Container\Injection;

use Fratily\Container\Injection\LazyInterface;
use Fratily\Container\Injection\LazyResolver;
use Fratily\Container\Resolver\InstanceGenerator;

/**
 *
 * A generic factory to create repeated instances of a single class. Note that
 * it does not implement the LazyInterface, so it is not automatically invoked
 * when resolving params and setters.
 *
 * @package Aura.Di
 *
 */
class LazyNew implements LazyInterface{

    /**
     * @var InstanceGenerator
     */
    private $instanceGenerator;

    /**
     * @var mixed[]
     */
    private $parameters;

    /**
     * Constructor
     *
     * @param   InstanceGenerator   $instanceGenerator
     *  インスタンスジェネレーターオブジェクト
     * @param   mixed[] $parameters
     *  追加指定パラメータの連想配列
     */
    public function __construct(
        InstanceGenerator $instanceGenerator,
        $parameters = []
    ){
        if(!is_array($parameters) && !($parameters instanceof LazyInterface)){
            throw new \InvalidArgumentException;
        }

        $this->instanceGenerator    = $instanceGenerator;
        $this->parameters           = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function load(){
        return $this->instanceGenerator->generate(
            LazyResolver::resolveLazy($this->parameters)
        );
    }
}
