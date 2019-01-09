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

use Fratily\Container\Builder\ContainerBuilder;

/**
 *
 */
class ContainerFactory{

    /**
     * @var string[]
     */
    private $providers  = [];

    /**
     * コンテナを生成する
     *
     * @param   mixed[] $options
     * オプションの連想配列
     * @param   string  $resolver
     *  リゾルバクラス名
     *
     * @return  Container
     */
    public function create(array $options = [], string $resolver = Resolver::class){
        $builder    = new ContainerBuilder();
        $providers  = [];

        foreach ($this->providers as $provider) {
            $provider   = new $provider($builder);
            $provider[] = $provider;

            $provider->build($options);
        }

        $builder->lock();

        $container  = new Container(
            new Repository(
                $builder->getServices(),
                $builder->getParameters(),
                $builder->getInjections()
            ),
            $resolver
        );

        foreach ($providers as $provider) {
            $provider->modify($container);
        }

        return $container;
    }

    /**
     * プロバイダを追加する
     *
     * @param   string  $provider
     *  プロバイダークラス名
     * @param   bool    $prepend
     *  先頭に追加するか
     *
     * @return  $this
     */
    public function add(string $provider, bool $prepend = false){
        if(!is_subclass_of($provider, Builder\AbstractProvider::class)){
            $class = Builder\AbstractProvider::class;
            throw new \InvalidArgumentException(
                "{$provider} is not a provider class. The provider class must"
                . " be a subclass of {$class}."
            );
        }

        if($prepend){
            array_unshift($this->providers, $provider);
        }else{
            $this->providers[]  = $provider;
        }

        return $this;
    }
}