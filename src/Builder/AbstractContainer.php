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

use Fratily\Container\Container;

/**
 *
 */
abstract class AbstractContainer{

    /**
     * ビルダーにサービスの定義などを追加する
     *
     * @param   ContainerBuilder    $builder
     *  サービスコンテナビルダー
     * @param   mixed[] $options
     *  オプションの連想配列
     *
     * @return  void
     */
    abstract public static function build(
        ContainerBuilder $builder,
        array $options
    );

    /**
     * サービスを手動で変更する
     *
     * ビルダーで解決できない依存関係を手動で解決したりするのに使用する
     *
     * @param   Container   $container
     *  サービスコンテナ
     */
    public static function modify(Container $container){

    }
}