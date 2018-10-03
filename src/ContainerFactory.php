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

use Psr\Container\ContainerInterface;

/**
 *
 */
class ContainerFactory{

    /**
     * @var string[]
     */
    private $containers = [];

    /**
     * コンテナを生成する
     *
     * @param   mixed[] $options
     *  ビルダー作成時に参照するオプションの連想配列
     * @param   ContainerInterface[]    $delegate
     *  サービスコンテナに追加するデリゲートコンテナの配列
     *
     * @return  Container
     */
    public function create(array $options = [], array $delegate = []){
        foreach($delegate as $delegateContainer){
            if(!$delegateContainer instanceof ContainerInterface){
                $interface  = ContainerInterface::class;

                throw new \InvalidArgumentException(
                    "The delegate container must implement '{$interface}'."
                );
            }
        }

        $resolver       = new Builder\Resolver\Resolver();
        $services       = [];
        $taggedServices = [];

        foreach($this->containers as $container){
            $builder    = new Builder\ContainerBuilder($resolver);

            $container::build($builder, $options);

            $services       = array_merge($services, $builder->getServices());
            $taggedServices = array_merge_recursive(
                $taggedServices,
                $builder->getTaggedServicesId()
            );
        }

        $resolver->lock();

        $serviceContainer   = new Container(
            $resolver,
            $services,
            $taggedServices,
            $delegate
        );

        foreach($this->containers as $container){
            $container::modify($serviceContainer);
        }

        return $container;
    }

    /**
     * 設定クラスを追加する
     *
     * @param   string  $container
     *  コンテナクラス
     *
     * @return  $this
     */
    public function append(string $container){
        if(!is_subclass_of($container, Builder\AbstractContainer::class)){
            $class  = Builder\AbstractContainer::class;

            throw new \InvalidArgumentException(
                "The container definition class must inherit '{$class}'."
            );
        }

        $this->containers[] = $container;

        return $this;
    }

    /**
     * 設定クラスを追加する
     *
     * 先に実行されるように追加する
     *
     * @param   string  $container
     *  コンテナクラス
     *
     * @return  $this
     */
    public function prepend(string $container){
        if(!is_subclass_of($container, Builder\AbstractContainer::class)){
            $class  = Builder\AbstractContainer::class;

            throw new \InvalidArgumentException(
                "The container definition class must inherit '{$class}'."
            );
        }

        array_unshift($this->containers, $container);

        return $this;
    }
}