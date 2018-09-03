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

use Fratily\Container\Resolver\Resolver;
use Fratily\Container\Resolver\CallbackInvoker;

/**
 *
 */
class Lazy implements LazyInterface{

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var mixed
     */
    private $callback;

    /**
     * @var mixed[]
     */
    private $params;

    /**
     * @var CallbackInvoker|null
     */
    private $callbackInvoker;

    /**
     * Constructor.
     *
     * @param   mixed   $callback
     * @param   mixed[] $params
     */
    public function __construct(Resolver $resolver, $callback, array $params = []){
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

        $this->resolver = $resolver;
        $this->callback = $callback;
        $this->params   = $params;
    }

    /**
     * {@inheritdoc}
     *
     * @throw LogicException
     */
    public function load(){
        if(null === $this->callbackInvoker){
            $this->callback = is_array($this->callback)
                ? LazyResolver::resolveLazyArray($this->callback)
                : LazyResolver::resolveLazy($this->callback)
            ;

            if(!is_callable($this->callback)){
                throw new \LogicException;
            }

            $this->callbackInvoker  = new CallbackInvoker($this->resolver, $this->callback);
        }

        return $this->callbackInvoker->invoke($this->params);
    }
}