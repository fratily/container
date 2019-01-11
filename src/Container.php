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
namespace Fratily\Container;

use Fratily\Container\Builder\LazyBuilder;
use Fratily\Container\Builder\Value\Injection;
use Fratily\Container\Builder\Value\Type;
use Fratily\Container\Builder\Value\Lazy\LazyResolver;
use Fratily\Reflection\ReflectionCallable;
use Psr\Container\ContainerInterface;

/**
 *
 */
class Container implements ContainerInterface{

    const REGEX_KEY = "/\A[A-Z_][0-9A-Z_]*(\.[A-Z_][0-9A-Z_]*)*\z/i";

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var LazyBuilder
     */
    private $lazyBuilder;

    /**
     *@var object[]
     */
    private $services   = [];

    /**
     * @var mixed[]
     */
    private $parameters = [];

    /**
     * Constructor
     *
     * @param   Repository  $repository
     *  リポジトリ
     * @param   string  $resolver
     *  リゾルバクラス名
     * @param   LazyBuilder $lazyBuilder
     *  遅延取得インスタンスビルダー
     */
    public function __construct(
        Repository $repository,
        string $resolver = Resolver::class,
        LazyBuilder $lazyBuilder = null
    ){
        $this->repository   = $repository;
        $this->lazyBuilder  = $lazyBuilder ?? new LazyBuilder();

        if(
            !class_exists($resolver)
            || !(Resolver::class === $resolver || is_subclass_of($resolver, Resolver::class))
        ){
            throw new \InvalidArgumentException();
        }

        $this->resolver = new $resolver($this);
    }

    /**
     * リポジトリを取得する
     *
     * @return  Repository
     */
    public function getRepository(){
        return $this->repository;
    }

    /**
     * リゾルバを取得する
     *
     * @return  Resolver
     */
    public function getResolver(){
        return $this->resolver;
    }

    /*
     * 遅延取得インスタンスビルダーを取得する
     *
     * @return  LazyBuilder
     */
    public function getLazyBuilder(){
        return $this->lazyBuilder;
    }

    /**
     * インスタンスを生成する
     *
     * @param   string $class
     *  対象クラス名
     * @param   Injection $addendInjection
     *  追加DI設定
     *
     * @return  object
     *
     * @throws  \InvalidArgumentException
     */
    public function new(string $class, Injection $addendInjection = null){
        if(!$this->getResolver()->isInstantiable($class)){
            throw new \InvalidArgumentException();
        }

        $instance   = null;
        $parameters = null;
        $reflection = new \ReflectionClass($class);
        $injections = $this->getRepository()->getInjectionsFromClass($class);

        if(null !== $addendInjection){
            array_unshift($injections, $addendInjection);
        }

        if(null !== $reflection->getConstructor()){
            $positions      = [];
            $names          = [];
            $types          = [];
            $mainInjection  = $this->getRepository()->hasInjection($class)
                ? $this->getRepository()->getInjection($class)
                : null
            ;

            foreach($injections as $injection){
                if($addendInjection === $injection || $mainInjection === $injection){
                    $positions  += $injection->getParameters(Injection::PARAM_POS);
                }

                $names  += $injection->getParameters(Injection::PARAM_NAME);
                $types  += $injection->getParameters(Injection::PARAM_TYPE);
            }

            try{
                $parameters = $this->getResolver()->resolveFunctionParameters(
                    $reflection->getConstructor(),
                    $positions,
                    $names,
                    $types
                );
            }catch(\Exception $e){
                throw new Exception\ClassInstantiationException(
                    "Failed to resolve parameters of {$class}::__construct().",
                    0,
                    $e
                );
            }
        }

        try{
            $instance   = null === $parameters
                ? $reflection->newInstance()
                : $reflection->newInstanceArgs(LazyResolver::resolveArray($parameters))
            ;
        }catch(\TypeError | \ArgumentCountError $e){
            if(
                $class === $e->getTrace()[0]["class"]
                && "__construct" === $e->getTrace()[0]["function"]
            ){
                throw new Exception\ClassInstantiationException(
                    "Failed to resolve parameters of {$class}::__construct().",
                    0,
                    $e
                );
            }

            throw $e;
        }

        foreach($injections as $injection){
            foreach($injection->getSetters() as $method => $args){
                call_user_func_array(
                    [$instance, $method],
                    LazyResolver::resolveArray($this, $args)
                );
            }
        }

        return $instance;
    }

    /**
     * コールバックを実行しその結果を取得する
     *
     * @param   callable    $callback
     *  実行対象コールバック
     * @param   mixed[] $positions
     *  ポジション指定パラメータ値リスト
     * @param   mixed[] $names
     *  名前指定パラメータ値リスト
     * @param   mixed[] $types
     *  クラス型指定パラメータ値リスト
     *
     * @return  mixed
     */
    public function invoke(
        callable $callback,
        array $positions,
        array $names,
        array $types
    ){
        try{
            $parameters = $this->getResolver()->resolveFunctionParameters(
                (new ReflectionCallable($callback))->getReflection(),
                $positions,
                $names,
                $types
            );
        }catch(\Exception $e){
            throw new \Exception("", 0, $e);
        }

        // TypeErrorやInvalidArgumentExceptionをキャッチしたいが、
        // コールバックの中の別の関数で発生することもありうるのでできない。
        return call_user_func_array($callback, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id){
        if(!is_string($id)){
            throw new \InvalidArgumentException();
        }

        if(!array_key_exists($id, $this->services)){
            if(!$this->has($id)){
                throw new Exception\ServiceNotFoundException();
            }

            $service    = $this->getRepository()->getService($id);
            $value      = $service->isLazy()
                ? $service->get()->load($this)
                : $service->get()
            ;

            if(!Type::valid($service->getType(), $value)){
                throw new \LogicException();
            }

            if(!is_object($value)){
                throw new \LogicException();
            }

            $this->services[$id]    = $value;
        }

        return $this->services[$id];
    }

    /**
     * タグ付けられたサービスの配列を取得する
     *
     * @param   string  $tag
     *  タグ
     * @param   bool    $useId4Index
     *  返り値のキーにサービスIDを使用するか
     *
     * @return  object[]
     */
    public function getWithTagged(string $tag, bool $useId4Index = false){
        $services   = [];

        foreach($this->getRepository()->getServiceIdsWithTagged($tag) as $id){
            if($useId4Index){
                $services[$id]  = $this->get($id);
            }else{
                $services[]     = $this->get($id);
            }
        }

        return $services;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  \InvalidArgumentException
     */
    public function has($id){
        if(!is_string($id)){
            throw new \InvalidArgumentException();
        }

        return $this->getRepository()->hasService($id);
    }

    /**
     * パラメーターを取得する
     *
     * @param   string  $id
     *  パラメーターID
     *
     * @return  mixed
     */
    public function getParameter(string $id){
        if(!array_key_exists($id, $this->parameters)){
            if(!$this->hasParameter($id)){
                throw new Exception\ParameterNotFoundException();
            }

            $parameter  = $this->getRepository()->getParameter($id);
            $value      = $parameter->isLazy()
                ? $parameter->get()->load($this)
                : $parameter->get()
            ;

            if(!Type::valid($parameter->getType(), $value)){
                throw new \LogicException();
            }

            $this->parameters[$id]  = $value;
        }

        return $this->parameters[$id];
    }

    /**
     * タグ付けされたパラメーターの配列を取得する
     *
     * @param   string $tag
     *  タグ名
     *
     * @param   bool    $useId4Index
     *  返り値のキーにパラメーターIDを使用するか
     *
     * @return  mixed[]
     */
    public function getParameterWithTagged(string $tag, bool $useId4Index = false){
        $parameters = [];

        foreach($this->getRepository()->getParameterIdsWithTagged($tag) as $id){
            if($useId4Index){
                $parameters[$id]    = $this->get($id);
            }else{
                $parameters[]       = $this->get($id);
            }
        }

        return $parameters;
    }

    /**
     * パラメーターが存在するか確認する
     *
     * @param   string  $id
     *  パラメーターID
     *
     * @return  bool
     */
    public function hasParameter(string $id){
        return $this->getRepository()->hasParameter($id);
    }
}