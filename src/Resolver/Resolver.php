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
            $this->classes[$class]  = new ClassResolver($this, $reflection);
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

    /**
     *　関数の引数を解決する
     *
     * @param \ReflectionFunctionAbstract $function
     *  解決対象関数のリフレクションインスタンス
     * @param   mixed[] $parameters
     *  パラメータの連想配列
     * @param   mixed[] $types
     *  型の連想配列
     *
     * @return  mixed[]
     *
     * @throws  Exception\RequireParameterNotDefinedException
     */
    public function parameterResolve(
        \ReflectionFunctionAbstract $function,
        array $parameters = [],
        array $types = []
    ){
        $result = [];

        foreach($function->getParameters() as $param){
            $pos    = $param->getPosition();
            $name   = $param->getName();
            $target = $function instanceof \ReflectionMethod
                ? $function->getDeclaringClass() . "::" . $function->getName()
                : $function->getName()
            ;

            if(array_key_exists($param->getPosition(), $parameters)){
                $result[]   = $parameters[$param->getPosition()];
                continue;
            }

            if(array_key_exists($param->getName(), $parameters)){
                $result[]   = $parameters[$param->getName()];
                continue;
            }

            if(null !== ($class = $param->getClass())){ // もし型宣言のクラスが不正なら例外発生
                if(array_key_exists($class->getName(), $types)){
                    if(null === $types[$class->getName()] && $param->allowsNull()){
                        $result[]   = null;
                        continue;
                    }

                    if(
                        is_object($types[$class->getName()])
                        && is_a($types[$class->getName()], $class->getName())
                    ){
                        $result[]   = $types[$class->getName()];
                        continue;
                    }

                    if(
                        $param->isDefaultValueAvailable()
                        && $types[$class->getName()] === $param->getDefaultValue()
                    ){
                        $result[]   = $types[$class->getName()];
                        continue;
                    }
                }

                if($this->hasType($class->getName())){
                    $result[]   = $this->getType($class->getName());
                    continue;
                }

                if(!$param->isDefaultValueAvailable()){
                    if(!$param->allowsNull() && !$class->isInstantiable()){
                        throw new Exception\RequireParameterNotDefinedException(
                            "The parameter \${$name}({$pos}) of {$target}() cannot be resolved."
                        );
                    }

                    $result[]   = $param->allowsNull()
                        ? null
                        : new LazyNew($this, $class->getName())
                    ;
                    continue;
                }
            }

            if($param->isDefaultValueAvailable()){
                $result[]   = $param->getDefaultValue();
                continue;
            }

            if($param->allowsNull()){
                $result[]   = null;
                continue;
            }

            throw new Exception\RequireParameterNotDefinedException(
                "The parameter \${$name}({$pos}) of {$target}() cannot be resolved."
            );
        }

        return $result;
    }
}
