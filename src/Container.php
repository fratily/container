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

use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;

/**
 *
 */
class Container implements ContainerInterface{

    const REGEX_KEY = "/\A[A-Z_][0-9A-Z_]*(\.[A-Z_][0-9A-Z_]*)*\z/i";

    const T_INT         = "int";
    const T_FLOAT       = "float";
    const T_BOOL        = "bool";
    const T_STRING      = "string";
    const T_ARRAY       = "array";
    const T_OBJECT      = "object";
    const T_CALLABLE    = "callable";
    const T_RESOURCE    = "resource";

    const TYPE_VALID    = [
        self::T_INT         => "is_int",
        self::T_FLOAT       => "is_float",
        self::T_BOOL        => "is_bool",
        self::T_STRING      => "is_string",
        self::T_ARRAY       => "is_array",
        self::T_OBJECT      => "is_object",
        self::T_CALLABLE    => "is_callable",
        self::T_RESOURCE    => "is_resource",
    ];

    /**
     * @var Builder\Resolver\Resolver
     */
    private $resolver;

    /**
     * @var object[]|Builder\Lazy\LazyInterface[]
     */
    private $services;

    /**
     * @var object[]
     */
    private $serviceInstances   = [];

    /**
     * @var string[][]
     */
    private $taggedServices;

    /**
     * @var ContainerInterface[]
     */
    private $delegateContainers;

    /**
     * Constructor
     *
     * @param   Resolver\Resolver   $resolver
     *  依存関係を統括するリゾルバオブジェクト
     * @param   object[]|Builder\Lazy\LazyInterface[]   $services
     *  サービスとそのIDの連想配列
     * @param   string[][]  $taggedServices
     *  タグ付けされたサービスの二次元連想配列
     * @param   ContainerInterface[]    $delegateContainers
     *  サービスコンテナに登録するデリゲートコンテナの配列
     */
    public function __construct(
        Builder\Resolver\Resolver $resolver,
        array $services,
        array $taggedServices,
        array $delegateContainers
    ){
        $this->resolver             = $resolver;
        $this->services             = $services;
        $this->taggedServices       = $taggedServices;
        $this->delegateContainers   = $delegateContainers;
    }

    /**
     * リゾルバを取得する
     *
     * @return  Builder\Resolver\Resolver
     */
    public function getResolver(){
        return $this->resolver;
    }

    /**
     * インスタンスを生成する
     *
     * @param   string  $class
     *  対象クラス名
     * @param   mixed[] $parameters
     *  追加指定パラメータの連想配列
     * @param   mixed[] $types
     *  追加指定型の連想配列
     *
     * @return  object
     */
    public function getInstance(
        string $class,
        array $parameters = [],
        array $types = []
    ){
        if(!class_exists($class)){
            throw new \InvalidArgumentException("Class '{$class}' not found.");
        }

        return $this->resolver
            ->getClassResolver($class)
            ->getInstanceGenerator()
            ->generate($this, $parameters, $types)
        ;
    }

    /**
     * コールバックを実行しその結果を取得する
     *
     * DI定義を用いたパラメータの自動解決が行われる。
     *
     * @param   callable    $callback
     *  実行対象コールバック
     * @param   mixed[] $parameters
     *  パラメータの連想配列
     * @param   mixed[] $types
     *  型の連想配列
     *
     * @return  mixed
     */
    public function invokeCallback(
        callable $callback,
        array $parameters = [],
        array $types = []
    ){
        return $this->resolver
            ->createCallbackInvoker($callback)
            ->invoke($this, $parameters, $types)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id){
        if(!is_string($id)){
            throw new \InvalidArgumentException(
                "The service ID must be a string."
            );
        }

        if(!$this->has($id)){
            throw new Exception\ServiceNotFoundException(
                "Service '{$id}' is not found in container."
            );
        }

        if(!array_key_exists($id, $this->serviceInstances)){
            $this->serviceInstances[$id]    = $this->hasInThisContainer($id)
                ? Builder\Lazy\LazyResolver::resolveLazy($this, $this->services[$id])
                : $this->getFromDelegateContainer($id)
            ;
        }

        return $this->serviceInstances[$id];
    }

    /**
     * タグ付けられたサービスの配列を取得する
     *
     * @param   string  $tag
     *  タグ
     *
     * @return  object[]
     */
    public function getTagged(string $tag){
        if(!array_key_exists($tag, $this->taggedServices)){
            return [];
        }

        $result = [];

        foreach($this->taggedServices[$tag] as $id){
            $result[]   = $this->get($id);
        }

        return $result;
    }

    /**
     * タグ付けられたサービスのIDの配列を取得する
     *
     * @param   string  $tag
     *  タグ
     *
     * @return  object[]
     */
    public function getTaggedIdList(string $tag){
        if(!array_key_exists($tag, $this->taggedServices)){
            return [];
        }

        return $this->taggedServices[$tag];
    }

    /**
     * デリゲートコンテナからサービスを取得する
     *
     * @param   string  $id
     *
     * @return  mixed
     *
     * @throws  \InvalidArgumentException
     * @throws  Exception\DelegateContainerException
     * @throws  Exception\ServiceNotFoundException
     */
    protected function getFromDelegateContainer(string $id){
        try{
            foreach($this->delegateContainers as $container){
                if($container->has($id)){
                    return $container->get($id);
                }
            }
        }catch(ContainerExceptionInterface $e){
            throw new Exception\DelegateContainerException(
                "Delegate container threw an exception.",
                0,
                $e
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws  \InvalidArgumentException
     */
    public function has($id){
        if(!is_string($id)){
            throw new \InvalidArgumentException(
                "The service ID must be a string."
            );
        }

        return $this->hasInThisContainer($id) || $this->hasInDelegateContainer($id);
    }

    /**
     * このコンテナに指定したIDのサービスが存在するか確認する
     *
     * @param   string  $id
     *
     * @return  bool
     *
     * @throws  \InvalidArgumentException
     */
    protected function hasInThisContainer(string $id){
        return array_key_exists($id, $this->services);
    }

    /**
     * デリゲートコンテナに指定したIDのサービスが存在するか確認する
     *
     * @param   string  $id
     *
     * @return  bool
     *
     * @throws  \InvalidArgumentException
     */
    protected function hasInDelegateContainer(string $id){
        foreach($this->delegateContainers as $container){
            if($container->has($id)){
                return true;
            }
        }

        return false;
    }
}