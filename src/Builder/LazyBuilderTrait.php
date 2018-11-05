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
     * @return  Lazy\Lazy
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
     * @return  Lazy\LazyCallable
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
     * @return  Lazy\LazyArray
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
     * @return  Lazy\LazyGet
     */
    public function lazyGet(string $id){
        return new Lazy\LazyGet($id);
    }

    /**
     * タグ付きサービス遅延取得インスタンスを生成する
     *
     * @param   string  $tag
     *  タグ名
     *
     * @return  Lazy\LazyGetTagged
     */
    public function lazyGetTagged(string $tag){
        return new Lazy\LazyGetTagged($tag);
    }

    /**
     * タグ付きサービスID遅延取得インスタンスを生成する
     *
     * @param   string  $tag
     *  タグ名
     *
     * @return  Lazy\LazyGetTaggedIdList
     */
    public function lazyGetTaggedIdList(string $tag){
        return new Lazy\LazyGetTaggedIdList($tag);
    }

    /**
     * ファイル遅延取得インスタンスを生成する
     *
     * @param   string|Lazy\LazyInterface|\SplFileInfo   $file
     *  includeするファイル
     * @param   bool    $once
     *  include_onceフラグ
     *
     * @return  Lazy\LazyInclude
     */
    public function lazyInclude($file, bool $once = false){
        return new Lazy\LazyLoadFile($file, $once, false);
    }

    /**
     * ファイル遅延取得インスタンスを生成する
     *
     * @param   string|Lazy\LazyInterface|\SplFileInfo   $file
     *  requireするファイル
     * @param   bool    $once
     *  require_onceフラグ
     *
     * @return  Lazy\LazyRequire
     */
    public function lazyRequire($file, bool $once = false){
        return new Lazy\LazyLoadFile($file, $once, true);
    }

    /**
     * インスタンス遅延生成インスタンスを生成する
     *
     * @param   string  $class
     *  クラス名
     * @param   mixed[] $parameters
     *  追加指定パラメータの配列
     * @param   mixed[] $types
     *  追加指定型指定解決値の配列
     *
     * @return  Lazy\LazyNew
     */
    public function lazyNew(
        string $class,
        array $parameters = [],
        array $types = []
    ){
        return new Lazy\LazyNew($class, $parameters, $types);
    }

    /**
     * 共有値遅延取得インスタンスを生成する
     *
     * @param   string  $name
     *  共有値名
     *
     * @return  Lazy\LazyGetShareValue
     */
    public function LazyGetShareValue(string $name){
        return new Lazy\LazyGetShareValue($name);
    }

    /**
     * サービスコンテナ遅延取得インスタンスを生成する
     *
     * @return  Lazy\LazyGetContainer
     */
    public function lazyGetContainer(){
        return new Lazy\LazyGetContainer();
    }

    /**
     * スーパーグローバル遅延取得インスタンスを生成する
     *
     * @param   int $type
     *  取得タイプ
     * @param   string  $name
     *  変数名
     * @param   bool    $checkInput
     *  filter_input等を通じてリクエスト時に入力された値か確認するか
     *
     * @return  Lazy\LazyGetSuperGlobal
     */
    public function lazyGetSuperGlobal(int $type, string $name, bool $checkInput = false){
        return new Lazy\LazyGetSuperGlobal($type, $name, $checkInput);
    }
}