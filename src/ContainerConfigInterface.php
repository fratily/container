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
namespace Fratily\Container;

/**
 *
 */
interface ContainerConfigInterface{

    /**
     * パラメータやセッターの値、サービスの定義を行う
     *
     * @param   Container   $container
     *
     * @return  void
     */
    public function define(Container $container);

    /**
     * DIコンテナを利用して実行する必要がある処理を実行する
     *
     * @param   Container   $container
     *
     * @return  void
     */
    public function modify(Container $container);
}