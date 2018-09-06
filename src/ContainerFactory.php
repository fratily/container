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
class ContainerFactory{

    /**
     * @var ContainerConfigInterface[]
     */
    private $configList = [];

    /**
     * コンテナを生成する
     *
     * @return  Container
     */
    public function create(){
        $container  = $this->createWithoutConfigure();

        foreach($this->configList as $config){
            $config->define($container);
        }

        $container->lock();

        foreach($this->configList as $config){
            $config->modify($container);
        }

        return $container;
    }

    /**
     * 設定を行わずにコンテナインスタンスを生成する
     *
     * @return  Container
     */
    public function createWithoutConfigure(){
        return new Container(new Resolver\Resolver());
    }

    /**
     * 設定クラスを追加する
     *
     * @param   ContainerConfigInterface    $config
     *
     * @return  $this
     */
    public function append(ContainerConfigInterface $config){
        $this->configList[] = $config;

        return $this;
    }

    /**
     * 設定クラスを追加する
     *
     * 先に実行されるように追加する
     *
     * @param   ContainerConfigInterface    $config
     *
     * @return  $this
     */
    public function prepend(ContainerConfigInterface $config){
        array_unshift($this->configList, $config);

        return $this;
    }
}