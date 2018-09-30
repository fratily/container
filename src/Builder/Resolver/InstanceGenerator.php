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

use Fratily\Container\Container;
use Fratily\Container\Builder\Lazy\LazyResolver;

/**
 *
 */
class InstanceGenerator{

    use LockTrait;

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
     */
    public function __construct(Resolver $resolver, string $class){
        if(
            !class_exists($class)
            || !$resolver->getClassResolver($class)->getReflection()->isInstantiable()
        ){
            throw new \InvalidArgumentException();
        }

        $this->resolver = $resolver;
        $this->class    = $class;
        $this->scope    = self::SINGLETON;
    }

    /**
     * インスタンスの生成に関する制約を指定する
     *
     * @param   string  $scope
     *  生成制約
     *
     * @return  $this
     *
     * @throws  \InvalidArgumentException
     */
    public function setScope(string $scope){
        if($this->locked()){
            throw new Exception\LockedException("Container is locked.");
        }

        if(self::SINGLETON !== $scope && self::PROTOTYPE !== $scope){
            throw new \InvalidArgumentException();
        }

        $this->scope    = $scope;

        return $this;
    }

    /**
     * インスタンスを生成する
     *
     * @param   Container   $container
     *  サービスコンテナ
     * @param   mixed[] $parameters
     *  追加指定パラメータの連想配列
     * @param   mixed[] $types
     *  追加指定型の連想配列
     *
     * @return  object
     */
    public function generate(Container $container, array $parameters = [], array $types = []){
        if(self::SINGLETON === $this->scope && null !== $this->instance){
            return $this->instance;
        }

        $instance       = $this->getReflection()->newInstanceWithoutConstructor();
        $constructor    = $this->getReflection()->getConstructor();

        if(null !== $constructor){
            $constructor->invokeArgs(
                $instance,
                LazyResolver::resolveLazyArray(
                    $container,
                    $this->resolveParameter($parameters, $types)
                )
            );
        }

        $this->ExecuteInjectionPropety($container, $instance);
        $this->ExecuteInjectionSetter($container, $instance);

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
     *  追加指定パラメータの連想配列
     * @param   mixed[] $types
     *  追加指定型の連想配列
     *
     * @return  mixed[]
     *
     * @throws  Exception\RequireParameterNotDefinedException
     * @throws  \ReflectionException
     *  型宣言に使用したクラスが存在しない場合などにスローされる
     */
    protected function resolveParameter(array $parameters = [], array $types = []){
        if(
            null === $this->getReflection()->getConstructor()
            || !$this->getReflection()->getConstructor()->isPublic()
        ){
            return [];
        }

        return $this->resolver->parameterResolve(
            $this->getReflection()->getConstructor(),
            $parameters
                + $this->getClassResolver()->getUnifiedParameters()
                + $this->getClassResolver()->getPostionParameters()
            ,
            $types
        );
    }

    /**
     * プロパティに値をインジェクションする
     *
     * @param   Container   $container
     *  サービスコンテナ
     * @param   object  $instance
     *  実行対象イスタンス
     *
     * @return  void
     */
    protected function ExecuteInjectionPropety(Container $container, $instance){
        $class      = $this->getReflection();
        $unified    = $this->getClassResolver()->getUnifiedProperties();

        do{
            $resolver   = $this->resolver->getClassResolver($class->getName());

            foreach($class->getProperties(\ReflectionProperty::IS_PRIVATE) as $prop){
                if(!$prop->isStatic() && $resolver->hasProperty($prop->getName())){
                    $prop->setAccessible(true);
                    $prop->setValue(
                        $instance,
                        LazyResolver::resolveLazy(
                            $container,
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
                LazyResolver::resolveLazy($container, $value)
            );
        }
    }

    /**
     * セッターを実行する
     *
     * @param   Container   $container
     *  サービスコンテナ
     * @param   object  $instance
     *  実行対象インスタンス
     *
     * @return  void
     */
    protected function ExecuteInjectionSetter(Container $container, $instance){
        $unified    = $this->getClassResolver()->getUnifiedSetters();

        foreach($unified as $name => $value){
            $reflection = $this->getReflection()->getMethod($name);

            $reflection->invoke(
                $instance,
                LazyResolver::resolveLazy($container, $value)
            );
        }
    }
}
