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
namespace Fratily\Container\Builder\Value;

/**
 *
 */
class LazyBuilder{

    /**
     * パラメータ自動解決を行うコールバック遅延実行インスタンスを生成する
     *
     * @param   mixed   $callback
     *  実行するコールバック
     *
     * @return  Lazy\Lazy
     */
    public static function lazy($callback){
        return new Lazy\Lazy($callback);
    }

    /**
     * 配列を遅延生成するインスタンスを生成する
     *
     * @param   mixed[] $values
     *  配列
     *
     * @return  Lazy\LazyArray
     */
    public static function lazyArray(array $values){
        return new Lazy\LazyArray($values);
    }

    /**
     * コールバック遅延実行インスタンスを生成する
     *
     * @param   mixed   $callback
     *  実行するコールバック
     *
     * @return  Lazy\LazyCallable
     */
    public static function lazyCallable($callback){
        return new Lazy\LazyCallable($callback);
    }

    /**
     * サービス遅延取得インスタンスを生成する
     *
     * @param   string|Lazy\LazyInterface    $id
     *  サービスID
     *
     * @return  Lazy\LazyGet
     */
    public static function lazyGet($id){
        return new Lazy\LazyGet($id);
    }

    /**
     * パラメーター遅延取得インスタンスを生成する
     *
     * @param   string|Lazy\LazyInterface    $id
     *  パラメーターID
     *
     * @return  Lazy\LazyGetParameter
     */
    public static function lazyGetParameter($id){
        return new Lazy\LazyGetParameter($id);
    }

    /**
     * タグ付きサービス遅延取得インスタンスを生成する
     *
     * @param   string|Lazy\LazyInterface    $tag
     *  タグ名
     *
     * @return  Lazy\LazyGetTagged
     */
    public static function lazyGetTagged($tag){
        return new Lazy\LazyGetTagged($tag);
    }

    /**
     * ファイル遅延取得インスタンスを生成する
     *
     * @param   string|Lazy\LazyInterface   $file
     *  読み込むファイル
     *
     * @return  Lazy\LazyInclude
     */
    public static function lazyLoadFile($file){
        return new Lazy\LazyLoadFile($file);
    }

    /**
     * インスタンス遅延生成インスタンスを生成する
     *
     * @param   string|Lazy\LazyInterface   $class
     *  クラス名
     *
     * @return  Lazy\LazyNew
     */
    public static function lazyNew(string $class){
        return new Lazy\LazyNew($class);
    }
}