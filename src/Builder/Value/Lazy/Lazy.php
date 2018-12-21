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
    protected function loadValue(Container $container){
        return $container->invokeCallback(
            $this->isLazyObject($this->callback)
                ? $this->callback->load($container, "callable")
                : $this->callback
            ,
            $this->parameters,
            $this->types
        );
    }

    /**
     * パラメータ自動解決用の値を追加する
     *
     * @param   string  $key
     *  ポジションもしくはパラメータ名
     * @param   mixed|LazyInterface $value
     *  値
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function parameter($key, $value){
        if($this->isLocked()){
            throw new LockedException();
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
     * @throws LockedException
     */
    public function type(string $class, $value){
        $class  = ltrim($class, "\\");

        if($this->isLocked()){
            throw new Exception\LockedException();
        }

        if(!class_exists($class) && !interface_exists($class)){
            throw new \InvalidArgumentException;
        }

        if(!is_object($value)){
            throw new \InvalidArgumentException;
        }

        if(!$this->isLazyObject($value) && !$value instanceof $class){
            throw new \InvalidArgumentException;
        }

        $this->types[$class]    = $value;

        return $this;
    }
}