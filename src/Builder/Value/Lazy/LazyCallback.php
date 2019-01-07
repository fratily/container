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
namespace Fratily\Container\Builder\Value\Lazy;

use Fratily\Container\Container;
use Fratily\Container\Builder\Exception\LockedException;

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
     * Constructor
     *
     * @param   mixed   $callback
     *  実行するコールバック
     */
    public function __construct($callback){
        if(!is_callable($callback) && !$this->isLazyObject($callback)){
            throw new \InvalidArgumentException();
        }

        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function load(Container $container){
        return call_user_func_array(
            $this->isLazyObject($this->callback)
                ? $this->callback->load($container, "callable")
                : $this->callback
            ,
            $this->isLazyObject($this->args)
                ? $this->args->load($container, "array")
                : $this->args
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
    public function args($args){
        if($this->isLocked()){
            throw new LockedException();
        }

        if(!is_array($args) && !$args instanceof LazyInterface){
            throw new \InvalidArgumentException;
        }

        $this->args = $args;

        return $this;
    }
}