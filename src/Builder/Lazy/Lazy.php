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
use Fratily\Container\Builder\Resolver\CallbackInvoker;

/**
 *
 */
class Lazy extends AbstractLazy{

    /**
     * @var mixed
     */
    private $callback;

    /**
     * @var mixed[]
     */
    private $parameters;

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

        if(null === $this->callbackInvoker){
            $callback   = $this->callback instanceof LazyInterface
                ? $this->callback->load($container, Container::T_CALLABLE)
                : $this->callback
            ;

            if(!is_callable($this->callback)){
                throw new \LogicException("ここに来ることはない");
            }

            $this->callbackInvoker  = new CallbackInvoker($container->getResolver(), $callback);
        }

        return $this->validType(
            $this->callbackInvoker->invoke(
                $container,
                $this->parameters,
                $this->types
            ),
            $expectedType
        );
    }

    /**
     * パラメータ自動解決用の値を追加する
     *
     * @param   string  $key
     *  ポジションもしくはパラメータ名
     * @param   mixed   $value
     *  値
     *
     * @return  $this
     *
     * @throws  Exception\LockedException
     */
    public function addParameter($key, $value){
        if($this->isLocked()){
            throw new Exception\LockedException();
        }

        if(!is_int($key) && !is_string($key)){
            throw new \InvalidArgumentException;
        }

        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * 型名自動解決用の値を追加する
     *
     * @param   string  $class
     *  クラス名
     * @param   object|LazyInterface    $value
     *  値
     *
     * @return  $this
     *
     * @throws Exception\LockedException
     */
    public function addType(string $class, $value){
        if($this->isLocked()){
            throw new Exception\LockedException();
        }

        if(!class_exists($class)){
            throw new \InvalidArgumentException;
        }

        $this->types[$class]    = $value;

        return $this;
    }
}