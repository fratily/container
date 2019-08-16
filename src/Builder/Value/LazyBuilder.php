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

use Fratily\Container\Builder\Value\Lazy\LazyArray;
use Fratily\Container\Builder\Value\Lazy\LazyCallback;
use Fratily\Container\Builder\Value\Lazy\LazyGet;
use Fratily\Container\Builder\Value\Lazy\LazyGetParameter;
use Fratily\Container\Builder\Value\Lazy\LazyGetTagged;
use Fratily\Container\Builder\Value\Lazy\LazyGetTaggedParameter;
use Fratily\Container\Builder\Value\Lazy\LazyInvoke;
use Fratily\Container\Builder\Value\Lazy\LazyNew;
use Fratily\Container\Builder\Value\Lazy\LazyPlain;

/**
 *
 */
class LazyBuilder
{

    /**
     * 内部値の遅延解決を行う配列(連想配列)を生成する
     *
     * @return  LazyArray
     */
    public function array()
    {
        return new LazyArray();
    }

    /**
     * 遅延実行するコールバックを生成する
     *
     * @return  LazyCallback
     */
    public function callback()
    {
        return new LazyCallback();
    }

    /**
     * 遅延取得するサービス値を生成する
     *
     * @return  LazyGet
     */
    public function get()
    {
        return new LazyGet();
    }

    /**
     * 遅延取得するパラメーター値を生成する
     *
     * @return  LazyGetParameter
     */
    public function getParameter()
    {
        return new LazyGetParameter();
    }

    /**
     * 遅延取得するタグ付きサービスのリストを生成する
     *
     * @return  LazyGetTagged
     */
    public function getTagged()
    {
        return new LazyGetTagged();
    }

    /**
     * 遅延取得するタグ付きパラメータリストを生成する
     *
     * @return  LazyGetTaggedParameter
     */
    public function getTaggedParameter()
    {
        return new LazyGetTaggedParameter();
    }

    /**
     * パラメータ自動解決を行う遅延実行するコールバックを生成する
     *
     * @return  LazyInvoke
     */
    public function invoke()
    {
        return new LazyInvoke();
    }

    /**
     * 遅延生成されるインスタンスを生成する
     *
     * @return  LazyNew
     */
    public function new()
    {
        return new LazyNew();
    }

    /**
     * 値を生成する
     *
     * @return  LazyPlain
     */
    public function plain()
    {
        return new LazyPlain();
    }
}
