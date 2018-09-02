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
class InstanceGenerator{

    const SINGLETON = "singleton";
    const PROTOTYPE = "prototype";

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $scope;

    /**
     * @var object|null
     */
    private $instance;

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
        string $class,
        string $scope = self::SINGLETON
    ){
        if(
            !class_exists($class)
            || !$resolver->getClassResolver($class)->getReflection()->isInstantiable()
        ){
            throw new \InvalidArgumentException();
        }

        $this->resolver = $resolver;
        $this->class    = $class;
        $this->scope    = $scope;
    }

    /**
     * インスタンスを生成する
     *
     * @param   mixed[] $parameters
     *  追加指定パラメータの連想配列
     *
     * @return  object
     */
    public function generate(array $parameters = []){
        if(null !== $this->instance){
            return $this->instance;
        }

        $instance       = $this->getReflection()->newInstanceWithoutConstructor();
        $constructor    = $this->getReflection()->getConstructor();

        $this->ExecuteInjectionPropety($instance);

        if(null !== $constructor){
            $constructor->invokeArgs(
                $instance,
                LazyResolver::resolveLazyArray(
                    $this->resolveParameter($parameters)
                )
            );
        }

        $this->ExecuteInjectionSetter($instance);

        if(self::SINGLETON === $this->scope){
            $this->instance = $instance;
        }

        return $instance;
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
     * インスタンス生成時に使用するパラメータの解決を行う
     *
     * @param   mixed[] $parameters
     *  追加パラメータ
     *
     * @return  mixed[]
     *
     * @throws  Exception\RequireParameterNotDefinedException
     * @throws  \ReflectionException
     *  型宣言に使用したクラスが存在しない場合などにスローされる
     */
    protected function resolveParameter(array $parameters){
        if(null === $this->getReflection()->getConstructor()){
            return [];
        }

        $result     = [];
        $resolver   = $this->resolver->getClassResolver($this->class);
        $unified    = $resolver->getUnifiedParameters($this->resolver);

        foreach($this->getReflection()->getConstructor()->getParameters() as $param){
            if(array_key_exists($param->getPosition(), $parameters)){
                $result[]   = $parameters[$param->getPosition()];
                continue;
            }

            if(array_key_exists($param->getName(), $parameters)){
                $result[]   = $parameters[$param->getName()];
                continue;
            }

            if($resolver->hasPositionParameter($param->getPosition())){
                $result[]   = $resolver->getPositionParameter($param->getPosition());
                continue;
            }

            if(array_key_exists($param->getName(), $unified)){
                $result[]   = $unified[$param->getName()];
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
            $class  = $resolver->getReflection()->getName();

            throw new Exception\RequireParameterNotDefinedException(
                "The parameter \${$name}({$pos}) of {$class} cannot be resolved."
            );
        }

        return $result;
    }

    /**
     * プロパティに値をインジェクションする
     *
     * @param   object  $instance
     *  実行対象イスタンス
     *
     * @return  void
     */
    protected function ExecuteInjectionPropety($instance){
        $class      = $this->getReflection();
        $unified    = $this->getClassResolver()->getUnifiedProperties($this->resolver);

        do{
            $resolver   = $this->resolver->getClassResolver($class->getName());

            foreach($class->getProperties(\ReflectionProperty::IS_PRIVATE) as $prop){
                if(!$prop->isStatic() && $resolver->hasProperty($prop->getName())){
                    $prop->setAccessible(true);
                    $prop->setValue(
                        $instance,
                        LazyResolver::resolveLazy(
                            $this->resolver,
                            $resolver->getProperty($prop->getName())
                        )
                    );
                }
            }
        }while(false !== ($class = $class->getParentClass()));

        foreach($unified as $name => $value){
            $prop   = $this->getReflection()->getProperty($name);

            $prop->setAccessible(true);
            $prop->setValue(
                $instance,
                LazyResolver::resolveLazy($this->resolver, $value)
            );
        }
    }

    /**
     * セッターを実行する
     *
     * @param   object  $instance
     *  実行対象インスタンス
     *
     * @return  void
     */
    protected function ExecuteInjectionSetter($instance){
        $unified    = $this->getClassResolver()->getUnifiedSetters($this->resolver);

        foreach($unified as $name => $value){
            $reflection = $this->getReflection()->getMethod($name);

            $reflection->invoke($instance, $value);
        }
    }
}
