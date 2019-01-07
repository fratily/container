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
namespace Fratily\Container\Builder\Value\Lazy;

use Fratily\Container\Container;

/**
 *
 */
class LazyResolver{

    /**
     * 値が遅延解決インスタンスなら解決を行う
     *
     * @param   Container   $container
     *  サービスコンテナ
     * @param   mixed   $value
     *  解決対象値
     *
     * @return  mixed
     */
    public static function resolve(Container $container, $value){
        return (is_object($value) && $value instanceof LazyInterface)
            ? $value->load($container)
            : $value
        ;
    }

    /**
     * 配列に含まれる遅延解決インスタンスの解決を行う
     *
     * @param   Container   $container
     *  サービスコンテナ
     * @param   mixed[] $value
     *  解決対象値の配列
     *
     * @return  mixed[]
     */
    public static function resolveArray(Container $container, array $value){
        return array_map(
            [static::class, "resolve"],
            array_fill(0, count($value), $container),
            $value
        );
    }

    /**
     * 値が遅延解決インスタンスなら解決を行う
     *
     * @param   Container   $container
     *  サービスコンテナ
     * @param   mixed   $val
     *  解決対象値
     *
     * @return  mixed
     */
    public static function resolveLazy(Container $container, $val){
        return static::resolve($container, $val);
    }

    /**
     * 配列に含まれる遅延解決インスタンスの解決を行う
     *
     * @param   Container   $container
     *  サービスコンテナ
     * @param   mixed[] $value
     *  解決対象値の配列
     *
     * @return  mixed[]
     */
    public static function resolveLazyArray(Container $container, array $value){
        return static::resolveArray($container, $value);
    }
}