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
namespace Fratily\Container\Builder\Resolver;

use Fratily\Container\Builder\Lazy;

/**
 *
 */
class Resolver{

    use LockTrait;

    /**
     * @var ClassResolver[]
     */
    private $classes    = [];

    /**
     * @var ClassResolver
     */
    private $types      = [];

    /**
     * {@inheritdoc}
     */
    public function lock(){
        $this->lock = true;

        foreach($this->classes as $class){
            $class->lock();
        }

        return $this;
    }

    /**
     * クラスの依存解決インスタンスを取得する
     *
     * @param   string  $class
     *  クラス名
     *
     * @return  ClassResolver
     */
    public function getClassResolver(string $class){
        if(!class_exists($class) && !interface_exists($class) && !trait_exists($class)){
            throw new \InvalidArgumentException();
        }

        if(!array_key_exists($class, $this->classes)){
            $reflection = new \ReflectionClass($class);
            $class      = $reflection->getName();

            $this->classes[$class]  = new ClassResolver($this, $reflection);

            if($this->locked()){
                $this->classes[$class]->lock();
            }
        }

        return $this->classes[$class];
    }

    /**
     * コールバック実行インスタンスを取得する
     *
     * @apram   Resolver    $resolver
     *  リゾルバ
     * @param   callable    $callback
     *  実行するコールバック
     *
     * return CallbackInvoker
     */
    public function createCallbackInvoker(callable $callback){
        return new CallbackInvoker($this, $callback);
    }

    /**
     * 指定した型の値を取得する
     *
     * @param   string  $type
     *  型名
     *
     * @return  object|null
     */
    public function getType(string $type){
        return $this->types[$type] ?? null;
    }

    /**
     * 指定した型のパラメータにインジェクションする値を登録する
     *
     * @param   string  $type
     *  型名
     * @param   object|Lazy\LazyInterface   $value
     *  インジェクションする値
     *
     * @return  $this
     */
    public function addType(string $type, $value){
        if($this->locked()){
            throw new Exception\LockedException("Container is locked.");
        }

        if(!class_exists($type) && !interface_exists($type)){
            throw new \InvalidArgumentException();
        }

        if(!is_object($value)){
            throw new \InvalidArgumentException();
        }

        $type  = $this->getClassResolver($type)->getReflection()->getName();

        $this->types[$type]    = $value;

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
                        : new LazyNew($class->getName())
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
