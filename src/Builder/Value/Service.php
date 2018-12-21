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
namespace Fratily\Container\Builder\Value;

use Fratily\Container\Builder\Lazy\LazyInterface;
use Fratily\Container\Builder\Exception\LockedException;
use Fratily\Container\Builder\LockableInterface;
use Fratily\Container\Builder\LockableTrait;

/**
 *
 */
class Service implements LockableInterface{

    use TagTrait, LockableTrait;

    const PROP_POS  = "pos";
    const PROP_NAME = "name";

    /**
     * @var string
     */
    private $class;

    /**
     * @var LazyInterface|object
     */
    private $value;

    /**
     * @var mixed[]
     */
    private $parameters = [
        self::PROP_POS  => [],
        self::PROP_NAME => [],
    ];

    /**
     * @var string[]
     */
    private $setters    = [];

    /**
     * Constructor
     *
     * @param   string  $class
     *  クラス名
     * @param   LazyInterface|object|string|null    $value
     *  サービスの値
     */
    public function __construct(string $class, $value = null){
        if(null !== $value && !is_object($value)){
            throw new \InvalidArgumentException;
        }

        $this->class    = ltrim($class, "\\");

        if(null !== $value){
            $this->set($value);
        }
    }

    /**
     * クラス名を取得する
     *
     * @return  string
     */
    public function getClass(){
        return $this->class;
    }

    /**
     * 値を取得する
     *
     * @return  Lazy\LazyInterface|object|null
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * 値を設定する
     *
     * @param   LazyInterface|object|string $value
     *  サービスの値
     *
     * @return  $this
     */
    public function set($value){
        if($this->isLocked()){
            throw new LockedException();
        }

        if(is_string($value) && class_exists($value)){
            if(
                $this->class !== ltrim($value, "\\")
                && !is_subclass_of($value, $this->class)
            ){
                throw new \InvalidArgumentException;
            }

            $value  = new Lazy\LazyNew($class);
        }elseif(!is_object($value)){
            throw new \InvalidArgumentException;
        }

        if(!$value instanceof LazyInterface && !$value instanceof $this->class){
            throw new \InvalidArgumentException;
        }

        $this->value    = $value;

        return $this;
    }

    /**
     * パラメータのリストを取得する
     *
     * @return  mixed[][]
     */
    public function getParameters(){
        return $this->parameters;
    }

    /**
     * パラメーターを追加する
     *
     * @param   int|string  $key
     *  パラメーターキー
     * @param   mixed   $value
     *  パラメータに渡す値
     *
     * @return  $this
     */
    public function parameter($key, $value){
        if($this->isLocked()){
            throw new LockedException();
        }

        if(!is_int($key) && !is_string($key)){
            throw new \InvalidArgumentException;
        }

        $this->parameters[is_int($key) ? self::PROP_POS : self::PROP_NAME]  = $value;

        return $this;
    }

    /**
     * セッターのリストを取得する
     *
     * @return  array[]
     */
    public function getSetters(){
        return $this->setters;
    }

    /**
     * セッターを追加する
     *
     * @param   string  $method
     *  メソッド名
     * @param   mixed   ...$args
     *  セッター実行時に渡す引数
     */
    public function setter(string $method, ...$args){
        if($this->isLocked()){
            throw new LockedException();
        }

        $this->setters[$method] = $args;

        return $this;
    }
}