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
namespace Fratily\Container\Builder;

/**
 *
 */
trait LazyBuilderTrait{

    /**
     * パラメータ自動解決を行うコールバック遅延実行インスタンスを生成する
     *
     * @param   mixed   $callback
     *  実行するコールバック
     * @param   mixed[] $parameters
     *  追加指定パラメータの配列
     * @param   mixed[] $types
     *  追加指定型指定解決値の配列
     *
     * @return  Lazy
     */
    public function lazy($callback, array $parameters = [], array $types = []){
        return new Lazy\Lazy($callback, $parameters, $types);
    }

    /**コールバック遅延実行インスタンスを生成する
     *
     * @param   mixed   $callback
     *  実行するコールバック
     * @param   mixed[] $parameters
     *  コールバック実行時に指定するパラメータの配列
     *
     * @return  LazyCallable
     */
    public function lazyCallable($callback, array $parameters = []){
        return new Lazy\LazyCallable($callback, $parameters);
    }

    /**
     * 配列を遅延生成するインスタンスを生成する
     *
     * @param   mixed[] $array
     *  遅延解決インスタンスを含む配列
     *
     * @return  LazyArray
     */
    public function lazyArray(array $array){
        return new Lazy\LazyArray($array);
    }

    /**
     * サービス遅延取得インスタンスを生成する
     *
     * @param   string  $id
     *  サービスID
     *
     * @return  LazyGet
     */
    public function lazyGet(string $id){
        return new Lazy\LazyGet($id);
    }

    /**
     * ファイル遅延取得インスタンスを生成する
     *
     * @param   string|LazyInterface|\SplFileInfo   $file
     *  includeするファイル
     * @param   bool    $once
     *  include_onceフラグ
     *
     * @return  LazyInclude
     */
    public function lazyInclude($file, bool $once = false){
        return new Lazy\LazyLoadFile($file, $once, false);
    }

    /**
     * ファイル遅延取得インスタンスを生成する
     *
     * @param   string|LazyInterface|\SplFileInfo   $file
     *  requireするファイル
     * @param   bool    $once
     *  require_onceフラグ
     *
     * @return  LazyRequire
     */
    public function lazyRequire($file, bool $once = false){
        return new Lazy\LazyLoadFile($file, $once, true);
    }

    /**
     * インスタンス遅延生成インスタンスを生成する
     *
     * @param   string  $class
     *  クラス名
     *
     * @return  LazyNew
     */
    public function lazyNew(string $class){
        return new Lazy\LazyNew($class);
    }
}