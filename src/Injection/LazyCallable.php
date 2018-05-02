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
class LazyCallable implements LazyInterface{

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
                if(!isset($callable[0]) || !isset($callable[1])
                    || (!is_string($callable[0]) && !is_object($callable[0]))
                    || (!is_string($callable[1]) && !is_object($callable[1]))
                ){
                    throw new \InvalidArgumentException();
                }

                $callable   = [$callable[0], $callable[1]];
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
        if(is_array($this->callable)){
            $this->callable = array_map(function($v) use ($this){
                $this->resolveLazy($v);
            }, $this->callable);
        }else{
            $this->callable = LazyResolver::resolveLazy($this->callable);
        }

        if(!is_callable($callable)){
            throw new \LogicException;
        }

        foreach($this->params as $key => $val){
            $params[$key]   = LazyResolver::resolveLazy($val);
        }

        return call_user_func_array($callable, $params);
    }
}