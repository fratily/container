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

use Fratily\Container\Builder\Value\Injection;
use Fratily\Container\Builder\Value\Type;
use Fratily\Container\Builder\Value\Lazy\LazyInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;

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

    private $services   = [];

    private $parameters = [];

    /**
     * Constructor
     *
     * @param   Repository  $repository
     *  リポジトリ
     * @param   string  $resolver
     *  リゾルバクラス名
     */
    public function __construct(Repository $repository, string $resolver = Resolver::class){
        $this->repository   = $repository;

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

    /**
     * インスタンスを生成する
     *
     * @param   string  $class
     *  対象クラス名
     * @param   Injection   $injection
     *  追加DI設定
     *
     * @return  object
     */
    public function new(string $class, Injection $injection = null){
    }

    /**
     * コールバックを実行しその結果を取得する
     *
     * @param   callable    $callback
     *  実行対象コールバック
     * @param   mixed[] $parameters
     *  パラメータの連想配列。キーに指定する値で解決方法が変わる。
     *  数値ならパラメーターの位置で解決。文字列であればパラメータ名で解決。
     *  文字列かつクラスもしくはインターフェース名であれば型名で解決する。
     *
     * @return  mixed
     */
    public function invoke(callable $callback, array $parameters = []){
    }

    /**
     * {@inheritdoc}
     */
    public function get($id){
        if(!is_string($id)){
            throw new \InvalidArgumentException();
        }

        if(!array_key_exists($id, $this->services)){
            if(!$this->getRepository()->hasService($id)){
                throw new Exception\ServiceNotFoundException();
            }

            $service    = $this->getRepository()->getService($id);
            $value      = $service->get() instanceof LazyInterface
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
     *
     * @return  object[]
     */
    public function getWithTagged(string $tag){
    }

    /**
     * {@inheritdoc}
     *
     * @throws  \InvalidArgumentException
     */
    public function has($id){
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
    }

    /**
     * タグ付けされたパラメーターの配列を取得する
     *
     * @param   string  $tag
     *  タグ名
     *
     * @return  mixed[]
     */
    public function getParameterWithTagged(string $tag){
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

    }
}