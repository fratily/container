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

/**
 *
 */
class LazyResolver{

    public static function resolveLazy($val){
        return ($val instanceof LazyInterface) ? $val->load() : $val;
    }

    public static function resolveLazyArray(array $vals){
        return array_map([$this, "resolveLazy"], $vals);
    }
}