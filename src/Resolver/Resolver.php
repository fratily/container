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
namespace Fratily\Container\Resolver;

/**
 *
 */
class Resolver{

    /**
     * @var ClassResolver[]
     */
    private $classes    = [];

    /**
     * @var mixed[]
     */
    private $values     = [];

    /**
     * @var mixed[]
     */
    private $types      = [];

    /**
     * クラスの依存解決インスタンスを取得する
     *
     * @param   string  $class
     *  クラス名
     *
     * @return  ClassResolver
     *
     * @throws  \InvalidArgumentException
     */
    public function getClassResolver(string $class){
        if(!class_exists($class) && !interface_exists($class) && !trait_exists($class)){
            throw new \InvalidArgumentException();
        }

        $reflection = null;

        if(!array_key_exists($class, $this->classes)){
            $reflection = new \ReflectionClass($class);
            $class      = $reflection->getName();
        }

        if(null !== $reflection && !array_key_exists($class, $this->classes)){
            $this->classes[$class]  = new ClassResolver($reflection);
        }

        return $this->classes[$class];
    }

    /**
     * 指定した型の値を取得する
     *
     * @param   string  $class
     *  クラス名
     *
     * @return  mixed|null
     *  もし存在しなければnullが返る。明示的にnullを指定しているのか
     *  確認するには、hasType()メソッドを利用する。
     */
    public function getType(string $class){
        return $this->types[$class] ?? null;
    }

    /**
     * 指定した型の値が登録済みか確認する
     *
     * @param   string  $class
     *  クラス名
     *
     * @return  bool
     */
    public function hasType(string $class){
        return array_key_exists($class, $this->types);
    }

    /**指定した型のパラメータにインジェクションする値を登録する
     *
     * @param   string  $class
     * @param   object|Injection\LazyInterface  $instance
     *
     * @return  $this
     *
     * @throws  \InvalidArgumentException
     */
    public function addType(string $class, $instance){
        if(!class_exists($class) && !interface_exists($class)){
            throw new \InvalidArgumentException();
        }

        if(!is_object($instance)){
            throw new \InvalidArgumentException();
        }

        $class  = $this->getClassResolver($class)->getReflection()->getName();

        $this->types[$class]    = $instance;

        return $this;
    }

    /**
     * 指定した名前の依存解決に使用する値を取得する
     *
     * @param   $name
     *
     * @return  mixed|null
     *  もし存在しなければnullが返る。明示的にnullを指定しているのか
     *  確認するには、hasValue()メソッドを利用する。
     */
    public function getValue(string $name){
        return $this->values[$name] ?? null;
    }

    /**
     * 指定した名前の依存解決用の値が登録済みか確認する
     *
     * @param   string  $name
     *  名前
     *
     * @return  bool
     */
    public function hasValue(string $name){
        return array_key_exists($name, $this->values);
    }

    /**
     * 指定した名前の依存解決用の値を登録する
     *
     * @param   string  $name
     *  名前
     * @param   mixed   $value
     *  値
     *
     * @return  $this
     */
    public function addValue(string $name, $value){
        $this->values[$name]    = $value;

        return $this;
    }
}
