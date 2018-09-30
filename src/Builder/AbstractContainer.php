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
abstract class AbstractContainer{

    /**
     * ビルダーにサービスの定義などを追加する
     *
     * @param   ContainerBuilderInterface   $builder
     *  サービスコンテナビルダー
     * @param   mixed[] $options
     *  オプションの連想配列
     *
     * @return  void
     */
    abstract public static function build(
        ContainerBuilderInterface $builder,
        array $options
    );
}