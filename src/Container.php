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

    /**
     * @var Builder\Resolver\Resolver
     */
    private $resolver;

    /**
     * @var object[]|Builder\Lazy\LazyInterface[]
     */
    private $services;

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
            throw new \InvalidArgumentException();
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
            ->invoke($parameters, $types)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id){
        if(!is_string($id)){
            throw new \InvalidArgumentException();
        }

        if(!$this->has($id)){
            throw new Exception\ServiceNotFoundException(
                "Service {$id} is not found in container."
            );
        }

        return $this->hasInThisContainer($id)
            ? Builder\Lazy\LazyResolver::resolveLazy($this, $this->services[$id])
            : $this->getFromDelegateContainer($id)
        ;
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
    protected function getFromDelegateContainer($id){
        if(!is_string($id)){
            throw new \InvalidArgumentException();
        }

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
            throw new \InvalidArgumentException();
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
    protected function hasInThisContainer($id){
        if(!is_string($id)){
            throw new \InvalidArgumentException();
        }

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
    protected function hasInDelegateContainer($id){
        if(!is_string($id)){
            throw new \InvalidArgumentException();
        }

        if($this->delegate !== null){
            foreach($this->delegate as $container){
                if($container->has($id)){
                    return true;
                }
            }
        }

        return false;
    }
}