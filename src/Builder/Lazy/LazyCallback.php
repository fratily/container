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

use Fratily\Container\Container;

/**
 *
 */
class LazyCallback extends AbstractLazy{

    /**
     * @var mixed
     */
    private $callback;

    /**
     * @var array|LazyInterface
     */
    private $args   = [];

    /**
     * Constructor.
     *
     * @param   mixed   $callback
     *  実行するコールバック
     */
    public function __construct($callback){
        if(!is_callable($callback) && !$callback instanceof LazyInterface){
            throw new \InvalidArgumentException();
        }

        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function load(Container $container, string $expectedType = null){
        $this->lock();

        $callback   = $this->callback instanceof LazyInterface
            ? $this->callback->load($container, Container::T_CALLABLE)
            : $this->callback
        ;
        $args       = $this->args instanceof LazyInterface
            ? $this->args->load($container, Container::T_ARRAY)
            : $this->args
        ;

        if(!is_callable($callback) || !is_array($args)){
            throw new \LogicException("ここに来ることはない");
        }

        return $this->validType(
            call_user_func_array($callback, $args),
            $expectedType
        );
    }

    /**
     * 引数を設定する
     *
     * @param   array|LazyInterface $args
     *  引数
     *
     * @return  $this
     */
    public function setArgs($args){
        if($this->isLocked()){
            throw new Exception\LockedException();
        }

        if(!is_array($args) && !$args instanceof LazyInterface){
            throw new \InvalidArgumentException;
        }

        $this->args = $args;

        return $this;
    }
}