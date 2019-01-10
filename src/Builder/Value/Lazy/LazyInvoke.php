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
class LazyInvoke extends AbstractLazy{

    /**
     * @var callable|LazyInterface|null
     */
    private $callback;

    /**
     * @var mixed[]
     */
    private $positions  = [];

    /**
     * @var mixed[]
     */
    private $names      = [];

    /**
     * @var mixed[]
     */
    private $types      = [];

    /**
     * {@inheritdoc}
     */
    protected static function getDefaultType(): string{
        return "callable";
    }

    /**
     * {@inheritdoc}
     */
    protected static function getAllowTypes(): ?array{
        return ["callable"];
    }

    /**
     * {@inheritdoc}
     */
    public function loadValue(Container $container){
        if(null === $this->callback){
            throw new Exception\SettingIsNotCompletedException();
        }

        return $container->invoke(
            LazyResolver::resolve($container, $this->callback),
            $this->positions,
            $this->names,
            $this->types
        );
    }

    /**
     * 実行するコールバックを設定する
     *
     * @param   callable|LazyInterface  $callback
     *  実行するコールバック
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function callback($callback){
        if($this->isLocked()){
            throw new LockedException();
        }

        if(
            !is_callable($callback)
            && !(
                static::isLazyObject($callback)
                && "callable" === $callback->getType()
            )
        ){
            throw new \InvalidArgumentException();
        }

        $this->callback = $callback;

        return $this;
    }

    /**
     * パラメータ自動解決の位置指定値を追加する
     *
     * @param   int $position
     *  パラメータポジション
     * @param   mixed   $value
     *  パラメータ値
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function position(int $position, $value){
        if($this->isLocked()){
            throw new LockedException();
        }

        $this->positions[$position] = $value;

        return $this;
    }

    /**
     * パラメータ自動解決の名前指定値を追加する
     *
     * @param   string  $name
     *  パラメータ名
     * @param   mixed   $value
     *  パラメータ値
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function name(string $name, $value){
        if($this->isLocked()){
            throw new LockedException();
        }

        $this->names[$name] = $value;

        return $this;
    }

    /**
     * パラメータ自動解決の型名指定値を追加する
     *
     * @param   string  $class
     *  クラス名
     * @param   object|LazyInterface    $value
     *  パラメータ値
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function type(string $class, $value){
        if($this->isLocked()){
            throw new LockedException();
        }

        $class  = ltrim($class, "\\");

        if(!class_exists($class) && !interface_exists($class)){
            throw new \InvalidArgumentException;
        }

        if(!is_object($value)){
            throw new \InvalidArgumentException;
        }

        if(static::isLazyObject($value)){
            if(!$value instanceof $class){
                throw new \InvalidArgumentException();
            }
        }else{
            if($class !== $value->getType()){
                throw new \InvalidArgumentException();
            }
        }

        $this->types[$class]    = $value;

        return $this;
    }
}