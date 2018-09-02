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

use Fratily\Container\Injection\LazyResolver;
use Fratily\Container\Injection\LazyNew;
use Fratily\Container\Exception;

/**
 *
 */
class MethodInvoker{

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var \ReflectionMethod
     */
    private $method;

    /**
     * Constructor
     *
     * @apram   Resolver    $resolver
     *  リゾルバ
     * @param   string  $class
     *  クラス名
     * @param   string  $scope
     *  生成ルール
     */
    public function __construct(
        Resolver $resolver,
        \ReflectionMethod $method
    ){
        $this->resolver = $resolver;
        $this->method   = $method;
    }

    /**
     * メソッドを実行しその結果を取得する
     *
     * @param   mixed[] $parameters
     *  メソッド実行時パラメーター
     *
     * @return  mixed
     */
    public function invoke($instance, array $parameters = []){
        if(
            !is_object($instance)
            || get_class($instance) !== $this->method->getDeclaringClass()->getName()
        ){
            throw new \InvalidArgumentException();
        }

        return $this->method->invokeArgs(
            $instance,
            LazyResolver::resolveLazyArray(
                $this->resolveParameter($parameters)
            )
        );
    }

    /**
     * リフレクションを取得する
     *
     * @return  \ReflectionClass
     */
    protected function getReflection(){
        return $this->resolver->getClassResolver($this->class)->getReflection();
    }

    /**
     *
     *
     * @return  ClassResolver
     */
    protected function getClassResolver(){
        return $this->resolver->getClassResolver($this->class);
    }

    /**
     * メソッド実行時に使用するパラメータの解決を行う
     *
     * @param   mixed[] $parameters
     *  追加パラメータ
     *
     * @return  mixed[]
     *
     * @throws  Exception\RequireParameterNotDefinedException
     */
    protected function resolveParameter(array $parameters){
        $result = [];

        foreach($this->method->getParameters() as $param){
            if(array_key_exists($param->getPosition(), $parameters)){
                $result[]   = $parameters[$param->getPosition()];
                continue;
            }

            if(array_key_exists($param->getName(), $parameters)){
                $result[]   = $parameters[$param->getName()];
                continue;
            }

            if(null !== ($class = $param->getClass())){ // もし型宣言のクラスが不正なら例外発生
                if($this->resolver->hasType($class->getName())){
                    $result[]   = $this->resolver->getType($class->getName());
                    continue;
                }

                if(!$param->isDefaultValueAvailable()){
                    $result[]   = $param->allowsNull()
                        ? null
                        : new LazyNew($this->resolver, $class->getName())
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

            $pos    = $param->getPosition();
            $name   = $param->getName();
            $class  = $this->method->getDeclaringClass()->getName();
            $method = $this->method->getName();

            throw new Exception\RequireParameterNotDefinedException(
                "The parameter \${$name}({$pos}) of {$class}::{$method}() cannot be resolved."
            );
        }

        return $result;
    }
}
