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

    /**
     * Constructor
     *
     * @param   Repository  $repository
     *  リポジトリ
     */
    public function __construct(Repository $repository){
        $this->repository   = $repository;
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
     * インスタンスを生成する
     *
     * @param   string  $class
     *  対象クラス名
     * @param   Injection   $injection
     *  追加DI設定
     *
     * @return  object
     */
    public function getInstance(string $class, Injection $injection = null){
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
    public function invokeCallback(callable $callback, array $parameters = []){
    }

    /**
     * {@inheritdoc}
     */
    public function get($id){
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