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
namespace Fratily\Container\Builder\Lazy;

/**
 *
 */
interface LazyInterface{

    /**
     * 遅延実行用メソッド
     *
     * @param   \Fratily\Container\Container    $container
     *  サービスコンテナ
     *
     * @return  mixed
     */
    public function load(\Fratily\Container\Container $container);
}