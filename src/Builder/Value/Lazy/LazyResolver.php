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
     * @param   mixed   $val
     *  解決対象値
     *
     * @return  mixed
     */
    public static function resolveLazy(Container $container, $val){
        return ($val instanceof LazyInterface) ? $val->load($container) : $val;
    }

    /**
     * 配列に含まれる遅延解決インスタンスの解決を行う
     *
     * @param   Container   $container
     *  サービスコンテナ
     * @param   mixed[] $vals
     *  解決対象値の配列
     *
     * @return  mixed[]
     */
    public static function resolveLazyArray(Container $container, array $vals){
        $result = [];

        foreach($vals as $index => $val){
            $result[$index] = self::resolveLazy($container, $val);
        }

        return $result;
    }
}