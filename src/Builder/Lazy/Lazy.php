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

use Fratily\Container\Builder\Resolver\CallbackInvoker;

/**
 *
 */
class Lazy implements LazyInterface{

    /**
     * @var mixed
     */
    private $callback;

    /**
     * @var mixed[]
     */
    private $params;

    /**
     * @var mixed[]
     */
    private $types;

    /**
     * @var CallbackInvoker|null
     */
    private $callbackInvoker;

    /**
     * Constructor
     *
     * @param   mixed   $callback
     *  実行するコールバック
     * @param   mixed[] $parameters
     *  追加指定パラメータの配列
     * @param   mixed[] $types
     *  追加指定型指定解決値の配列
     */
    public function __construct($callback, array $parameters = [], array $types = []){
        if(!is_callable($callback)){
            if(is_array($callback)){
                if(!isset($callback[0]) || !isset($callback[1]) || count($callback) !== 2){
                    throw new \InvalidArgumentException();
                }

                if(!($callback[0] instanceof LazyInterface)
                    && !($callback[1] instanceof LazyInterface)
                ){
                    throw new \InvalidArgumentException();
                }
            }else if(!($callback instanceof LazyInterface)){
                throw new \InvalidArgumentException();
            }
        }

        $this->callback = $callback;
        $this->params   = $parameters;
        $this->types    = $types;
    }

    /**
     * {@inheritdoc}
     *
     * @throw LogicException
     */
    public function load(\Fratily\Container\Container $container){
        if(null === $this->callbackInvoker){
            $this->callback = is_array($this->callback)
                ? LazyResolver::resolveLazyArray($container, $this->callback)
                : LazyResolver::resolveLazy($container, $this->callback)
            ;

            if(!is_callable($this->callback)){
                throw new \LogicException;
            }

            $this->callbackInvoker  = new CallbackInvoker(
                $container->getResolver(),
                $this->callback
            );
        }

        return $this->callbackInvoker->invoke(
            $container,
            $this->params,
            $this->types
        );
    }
}