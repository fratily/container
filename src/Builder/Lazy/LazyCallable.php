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
class LazyCallable implements LazyInterface{

    /**
     * @var mixed
     */
    private $callback;

    /**
     * @var mixed[]
     */
    private $params;

    /**
     * Constructor.
     *
     * @param   mixed   $callback
     *  実行するコールバック
     * @param   mixed[] $parameters
     *  コールバック実行時に指定するパラメータの配列
     */
    public function __construct($callback, array $parameters = []){
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
    }

    /**
     * {@inheritdoc}
     *
     * @throw LogicException
     */
    public function load(\Fratily\Container\Container $container){
        $callback   = is_array($this->callback)
            ? LazyResolver::resolveLazyArray($this->callback)
            : LazyResolver::resolveLazy($this->callback)
        ;

        if(!is_callable($callback)){
            throw new \LogicException;
        }

        return call_user_func_array(
            $callback,
            LazyResolver::resolveLazyArray($this->params)
        );
    }
}