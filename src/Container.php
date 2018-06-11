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

use Psr\Container\{
    ContainerInterface,
    ContainerExceptionInterface
};

/**
 *
 */
class Container implements ContainerInterface{

    /**
     * @var bool
     */
    private $locked;

    /**
     * @var Injection\Factory
     */
    private $factory;

    /**
     * @var mixed[]
     */
    protected $services;

    /**
     * @var object[]
     */
    protected $instances;

    /**
     * @var \SplPriorityQueue|null
     */
    private $delegate;

    /**
     * Constructor
     *
     * @param   Injection\Factory   $factory
     */
    public function __construct(Injection\Factory $factory){
        $this->factory      = $factory;
        $this->locked       = false;
        $this->services     = [];
        $this->instances    = [];
    }

    /**
     * コンテナをロックする
     */
    public function lock(){
        $this->locked   = true;
    }

    /**
     * コンテナがロックされているか確認する
     *
     * @return  bool
     */
    public function isLocked(){
        return $this->locked;
    }

    /**
     * Creates and returns a new instance of a class using reflection and
     * the configuration parameters, optionally with overrides, invoking Lazy
     * values along the way.
     *
     * Note the that container must be locked before creating a new instance.
     * This prevents premature resolution of params and setters.
     *
     * @param   string  $class
     * @param   mixed[] $params
     * @param   mixed[] $setters
     *
     * @return  object
     */
    public function newInstance(
        $class,
        $params = [],
        $setters = []
    ){
        $this->lock();

        return $this->factory->newInstance($class, $params, $setters);
    }

    /**
     * {@inheritdoc}
     *
     * @throws  \InvalidArgumentException
     */
    public function get($id){
        if(!is_string($id)){
            throw new \InvalidArgumentException();
        }

        $this->lock();

        if(!array_key_exists($id, $this->instances)){
            if($this->hasInThisContainer($id)){
                $instance   = Injection\LazyResolver::resolveLazy(
                    $this->services[$id]
                );

                if(!is_object($instance)){
                    $e  = new Exception\ServiceNotObjectException();
                    $e->setId($id);

                    throw $e;
                }

                $this->instances[$id]   = $instance;
            }else if($this->hasInDelegateContainer($id)){
                return $this->getFromDelegateContainer($id);
            }else{
                $e  = new Exception\ServiceNotFoundException();
                $e->setId($id);

                throw $e;
            }
        }

        return $this->instances[$id];
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
    public function getFromDelegateContainer($id){
        if(!is_string($id)){
            throw new \InvalidArgumentException();
        }

        try{
            foreach($this->delegate as $container){
                if($container->has($id)){
                    return $container->get($id);
                }
            }
        }catch(ContainerExceptionInterface $e){
            throw new Exception\DelegateContainerException(null, 0, $e);
        }

        $e  = new Exception\ServiceNotFoundException();
        $e->setId($id);

        throw $e;
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
    public function hasInThisContainer($id){
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
    public function hasInDelegateContainer($id){
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

    /**
     * デリゲートコンテナを追加する
     *
     * @param   ContainerInterface  $container
     * @param   int $priority
     *
     * @return  $this
     *
     * @throws  Exception\LockedException;
     */
    public function addDelegateContainer(ContainerInterface $container, int $priority = 1){
        if($this->isLocked()){
            throw new Exception\LockedException();
        }

        if($this->delegate === null){
            $this->delegate = new \SplPriorityQueue();
        }

        $this->delegate->insert($container, $priority);

        return $this;
    }

    /**
     * コンテナにサービスを登録する
     *
     * 登録しようとしているサービスがクロージャだった場合、
     * 遅延ロード用の匿名関数であると解釈されます。
     *
     * @param   string  $id
     * @param   object|\Closure|Injection\LazyInterface $val
     *
     * @return  $this
     *
     * @throws  Exception\LockedException
     * @throws  \InvalidArgumentException
     */
    public function set(string $id, $val){
        if($this->isLocked()){
            throw new Exception\LockedException();
        }

        if(!is_object($val)){
            throw new \InvalidArgumentException();
        }

        $this->services[$id] = ($val instanceof \Closure)
            ? $this->lazyCallable($val) : $val
        ;

        return $this;
    }

    /**
     * コンストラクタインジェクションの値を追加
     *
     * @param   string  $class
   　* @param   string|int  $name
     * @param   mixed   $value
     *
     * @return  $this
     */
    public function param(string $class, $name, $value){
        if(!is_string($name) && !is_int($name)){
            throw new \InvalidArgumentException();
        }

        $this->factory->getResolver()->addParameter($class, $name, $value);

        return $this;
    }

    /**
     * コンストラクタインジェクションの値を追加
     *
     * @param   string  $class
   　* @param   mixed[] $params
     *
     * @return  $this
     */
    public function params(string $class, array $params){
        foreach($params as $name => $value){
            $this->param($class, $name, $value);
        }

        return $this;
    }

    /**
     * セッターインジェクションの値を追加
     *
     * @param   string  $class
   　* @param   string  $method
     * @param   mixed   $value
     *
     * @return  $this
     */
    public function setter(string $class, string $method, $value){
        $this->factory->getResolver()->addSetter($class, $method, $value);

        return $this;
    }

    /**
     * セッターインジェクションの値を追加
     *
     * @param   string  $class
   　* @param   mixed[] $setters
     *
     * @return  $this
     */
    public function setters(string $class, array $setters){
        foreach($setters as $method => $value){
            $this->setter($class, $method, $value);
        }

        return $this;
    }

    /**
     * コンストラクタインジェクションにおける自動解決用の値を追加
     *
     * @param   string  $class
     * @param   mixed   $value
     *
     * @return  $this
     */
    public function type(string $class, $value){
        $this->factory->getResolver()->addType($class, $value);

        return $this;
    }

    /**
     * コンストラクタインジェクションにおける自動解決用の値を追加
     *
     * @param   mixed[] $types
     *
     * @return  $this
     */
    public function types(array $types){
        foreach($types as $class => $value){
            $this->type($class, $value);
        }

        return $this;
    }

    /**
     * 値を追加
     *
     * @param   string  $name
     * @param   mixed   $value
     *
     * @return  $this
     */
    public function value(string $name, $value){
        $this->factory->getResolver()->addValue($name, $value);

        return $this;
    }

    /**
     * 値を追加
     *
     * @param   mixed[] $values
     *
     * @return  $this
     */
    public function values(array $values){
        foreach($values as $name => $value){
            $this->value($name, $value);
        }

        return $this;
    }

    /**
     * パラメータ自動解決を行う関数/メソッド遅延実行インスタンスを生成する
     *
     * @param   mixed   $callable
     * @param   mixed[] $params
     *
     * @return  Injection\Lazy
     */
    public function lazy($callable, $params = []){
        return $this->factory->createLazy($callable, $params);
    }

    /**
     * 関数/メソッド遅延実行インスタンスを生成する
     *
     * @param   mixed   $callable
     * @param   mixed[] $params
     *
     * @return  Injection\LazyCallable
     */
    public function lazyCallable($callable, ...$params){
        return $this->factory->createLazyCallable($callable, $params);
    }

    /**
     * 配列遅延生成を行うインスタンスを生成する
     *
     * @param   mixed[] $array
     *
     * @return  Injection\LazyArray
     */
    public function lazyArray(array $array){
        return $this->factory->createLazyArray($array);
    }

    /**
     * サービス遅延取得インスタンスを生成する
     *
     * @param   ContainerInterface  $container
     * @param   string  $id
     *
     * @return  Injection\LazyGet
     */
    public function lazyGet($id){
        return $this->factory->createLazyGet($this, $id);
    }

    /**
     * ファイル遅延取得インスタンスを生成する
     *
     * @param   string|LazyInterface|\SplFileInfo   $file
     *
     * @return  Injection\LazyInclude
     */
    public function lazyInclude($file){
        return $this->factory->createLazyInclude($file);
    }

    /**
     * ファイル遅延取得インスタンスを生成する
     *
     * @param   string|LazyInterface|\SplFileInfo   $file
     *
     * @return  Injection\LazyInclude
     */
    public function lazyRequire($file){
        return $this->factory->createLazyRequire($file);
    }

    /**
     * インスタンス遅延生成インスタンスを生成する
     *
     * @param   string  $class
     * @param   mixed[] $params
     * @param   mixed[] $setters
     *
     * @return  Injection\LazyNew
     */
    public function lazyNew($class, $params = [], $setters = []){
        return $this->factory->createLazyNew($class, $params, $setters);
    }

    /**
     * 値遅延取得インスタンスを生成する
     *
     * @param   string|LazyInterface  $key
     *
     * @return  Injection\LazyValue
     */
    public function lazyValue($key){
        return $this->factory->createLazyValue($key);
    }
}