<?php
/**
 * FratilyPHP Container
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento.oka@kentoka.com>
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
     * @var Resolver\Resolver
     */
    private $resolver;

    /**
     * @var \SplPriorityQueue|null
     */
    private $delegate;

    /**
     * @var string[]
     */
    protected $services = [];

    /**
     * @var object[]
     */
    protected $instances    = [];

    /**
     * Constructor
     *
     * @param   Resolver\Resolver   $resolver
     */
    public function __construct(Resolver\Resolver $resolver){
        $this->resolver = $resolver;
        $this->locked   = false;
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
     * デリゲートコンテナを追加する
     *
     * @param   ContainerInterface  $container
     * @param   int $priority
     *
     * @return  $this
     */
    public function addDelegateContainer(ContainerInterface $container, int $priority = 1){
        if($this->delegate === null){
            $this->delegate = new \SplPriorityQueue();
        }

        $this->delegate->insert($container, $priority);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id){
        $this->lock();

        if(!array_key_exists($id, $this->instances)){
            if(!$this->has($id)){
                $e  = new Exception\ServiceNotFoundException();
                $e->setId($id);

                throw $e;
            }

            if(!array_key_exists($id, $this->services)){
                try{
                    $find       = false;
                    $service    = null;

                    foreach($this->delegate as $container){
                        if($container->has($id)){
                            $find       = true;
                            $service    = $container->get($id);

                            break;
                        }
                    }
                }catch(ContainerExceptionInterface $e){
                    throw new Exception\DelegateContainerException(null, 0, $e);
                }

                if(!$find){
                    $e  = new Exception\ServiceNotFoundException();
                    $e->setId($id);

                    throw $e;
                }

                return $service;
            }

            $this->instances[$id]   = Injection\LazyResolver::resolveLazy(
                $this->services[$id]
            );
        }

        return $this->instances[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function has($id){
        return array_key_exists($id, $this->services) || $this->hasInDelegate($id);
    }

    /**
     *
     * @param   string  $id
     *
     * @return  bool
     */
    protected function hasInDelegate($id){
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

        $this->services[$id] = ($val instanceof Closure)
            ? $this->lazyCallable($val) : $val
        ;

        return $this;
    }

    /**
     * コンストラクタインジェクションの値を追加
     *
     * @param   string  $class
   　* @param   string  $param
     * @param   mixed   $value
     *
     * @return  $this
     */
    public function param(string $class, string $param, $value){
        $this->resolver->setParam($class, $param, $value);

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
        $this->resolver->setSetter($class, $method, $value);

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
        $this->resolver->setType($class, $value);

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
        $this->resolver->setValue($name, $value);

        return $this;
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
        string $class,
        array $params = [],
        array $setters = []
    ){
        $this->lock();

        return $this->resolver->resolve($class, $params, $setters)->create();
    }

    /**
     *
     *
     * @param   callable    $callable
     * @param   mixed   ...$params
     *
     * @return  Injection\Lazy
     *
     * @throws  \InvalidArgumentException
     */
    public function lazy($callable, ...$params){
        return new Injection\Lazy($callable, $params);
    }

    /**
     *
     *
     * @param   mixed[] $valules
     *
     * @return  Injection\LazyArray
     */
    public function lazyArray(array $valules){
        return new Injection\LazyArray($valules);
    }

    /**
     *
     *
     * @param   callable    $callable
     * @param   mixed   ...$params
     *
     * @return  Injection\LazyCallable
     *
     * @throws  \InvalidArgumentException
     */
    public function lazyCallable($callable, ...$params){
        return new Injection\LazyCallable($callable, $params);
    }

    /**
     *
     *
     * @param   string  $id
     *
     * @return  Injection\LazyGet
     */
    public function lazyGet($id){
        return new Injection\LazyGet($this, $id);
    }

    /**
     *
     *
     * @param   string  $class
     * @param   mixed[] $params
     * @param   mixed[] $setters
     *
     * @return  Injection\LazyNew
     */
    public function lazyNew(
        string $class,
        array $params = [],
        array $setters = []
    ){
        return new Injection\LazyNew($this->resolver, $class, $params, $setters);
    }

    /**
     *
     * @param   mixed   $file
     *
     * @return  Injection\LazyInclude
     *
     * @throws  \InvalidArgumentException
     */
    public function lazyInclude($file){
        return new Injection\LazyInclude($file);
    }

    /**
     *
     * @param   mixed   $file
     *
     * @return  Injection\LazyRequire
     *
     * @throws  \InvalidArgumentException
     */
    public function lazyRequire($file){
        return new Injection\LazyRequire($file);
    }

    /**
     *
     *
     * @param   string  $key
     *
     * @return  Injection\LazyValue
     */
    public function lazyValue(string $key){
        return new Injection\LazyValue($this->resolver, $key);
    }
}