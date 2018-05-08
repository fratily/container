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
namespace Fratily\Container\Injection;

/**
 *
 */
class Lazy implements LazyInterface{

    /**
     * @var mixed
     */
    private $callable;

    /**
     * @var mixed[]
     */
    private $params;

    /**
     * Constructor.
     *
     * @param   mixed   $callable
     * @param   mixed[] $params
     */
    public function __construct($callable, array $params = []){
        if(!is_callable($callable)){
            if(is_array($callable)){
                if(!isset($callable[0]) || !isset($callable[1]) || count($callable) !== 2){
                    throw new \InvalidArgumentException();
                }

                if(!($callable[0] instanceof LazyInterface)
                    && !($callable[1] instanceof LazyInterface)
                ){
                    throw new \InvalidArgumentException();
                }
            }else if(!($callable instanceof LazyInterface)){
                throw new \InvalidArgumentException();
            }
        }

        $this->callable = $callable;
        $this->params   = $params;
    }

    /**
     * {@inheritdoc}
     *
     * @throw LogicException
     */
    public function load(){
        $this->params   = LazyResolver::resolveLazyArray($this->params);
        $this->callable = is_array($this->callable)
            ? LazyResolver::resolveLazyArray($this->callable)
            : LazyResolver::resolveLazy($this->callable)
        ;

        if(!is_callable($this->callable)){
            throw new \LogicException;
        }

        return call_user_func_array($this->callable, $this->params);
    }
}