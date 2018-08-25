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

use Fratily\Reflection\Reflector\ClassReflector;

/**
 *
 */
class ContainerFactory{

    /**
     * @var ClassReflector
     */
    private $reflector;

    /**
     * @var ContainerConfigInterface[]
     */
    private $configList = [];

    /**
     * Constructor
     *
     * @param   ClassReflector  $reflector
     */
    public function __construct(ClassReflector $reflector = null){
        $this->reflector    = $reflector ?? new ClassReflector();
    }

    /**
     * コンテナを生成する
     *
     * @return  Container
     */
    public function create(){
        $container  = new Container(
            new Injection\Factory(
                new Resolver\Resolver($this->reflector, $auto)
            )
        );

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
        array_shift($this->configList, $config);

        return $this;
    }
}