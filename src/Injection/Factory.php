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
namespace Fratily\Container\Injection;

use Psr\Container\ContainerInterface;
use Fratily\Container\Resolver\Resolver;

/**
 *
 */
class Factory{

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * Constructor
     *
     * @param   Resolver    $resolver
     */
    public function __construct(Resolver $resolver){
        $this->resolver = $resolver;
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
     * @param   string|LazyInterface    $class
     * @param   mixed[]|LazyInterface   $params
     * @param   mixed[]||LazyInterface  $setters
     *
     * @return  object
     */
    public function newInstance($class, $params = [], $setters = []){
        return LazyResolver::resolveLazy(
            $this->createLazyNew($class, $params, $setters)
        );
    }

    /**
     * パラメータ自動解決を行う関数/メソッド遅延実行インスタンスを生成する
     *
     * @param   mixed   $callable
     * @param   mixed[] $params
     *
     * @return  Lazy
     */
    public function createLazy($callable, $params = []){
        return new Lazy($this->resolver, $callable, $params);
    }

    /**
     * 関数/メソッド遅延実行インスタンスを生成する
     *
     * @param   mixed   $callable
     * @param   mixed[] $params
     *
     * @return  LazyCallable
     */
    public function createLazyCallable($callable, $params = []){
        return new LazyCallable($callable, $params);
    }

    /**
     * 配列遅延生成を行うインスタンスを生成する
     *
     * @param   mixed[] $array
     *
     * @return  LazyArray
     */
    public function createLazyArray($array){
        return new LazyArray($array);
    }

    /**
     * サービス遅延取得インスタンスを生成する
     *
     * @param   ContainerInterface  $container
     * @param   string  $id
     *
     * @return  LazyGet
     */
    public function createLazyGet($container, $id){
        return new LazyGet($container, $id);
    }

    /**
     * ファイル遅延取得インスタンスを生成する
     *
     * @param   string|LazyInterface|\SplFileInfo   $file
     *
     * @return  LazyInclude
     */
    public function createLazyInclude($file){
        return new LazyInclude($file);
    }

    /**
     * ファイル遅延取得インスタンスを生成する
     *
     * @param   string|LazyInterface|\SplFileInfo   $file
     *
     * @return  LazyInclude
     */
    public function createLazyRequire($file){
        return new LazyRequire($file);
    }

    /**
     * インスタンス遅延生成インスタンスを生成する
     *
     * @param   string  $class
     * @param   mixed[] $params
     * @param   mixed[] $setters
     *
     * @return  LazyNew
     */
    public function createLazyNew($class, $params = [], $setters = []){
        return new LazyNew($this->resolver, $class, $params, $setters);
    }

    /**
     * 値遅延取得インスタンスを生成する
     *
     * @param   string|LazyInterface  $key
     *
     * @return  LazyValue
     */
    public function createLazyValue($key){
        return new LazyValue($this->resolver, $key);
    }
}