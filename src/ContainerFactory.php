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

use Fratily\Container\Builder\AbstractProvider;
use Fratily\Container\Builder\ContainerBuilder;

/**
 *
 */
class ContainerFactory
{
    /**
     * @var \SplPriorityQueue|AbstractProvider[]
     */
    private $providers;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->providers = new \SplPriorityQueue();
    }

    /**
     * Create Container.
     *
     * @param Resolver $resolver The resolver
     *
     * @return Container
     */
    public function create(Resolver $resolver): Container
    {
        if (
            !class_exists($resolver)
            || !(Resolver::class === $resolver || is_subclass_of($resolver, Resolver::class))
        ) {
            throw new \InvalidArgumentException();
        }

        $builder = new ContainerBuilder();

        foreach ($this->providers as $provider) {
            $provider->build($builder);
        }

        $builder->lock();

        $container = new Container(new Repository($builder), $resolver);

        $resolver->setContainer($container);

        foreach ($this->providers as $provider) {
            $provider->modify($container);
        }

        return $container;
    }

    /**
     * Adds provider.
     *
     * @param AbstractProvider $provider The provider
     * @param int              $priority The provider priority
     *
     * @return $this
     */
    public function add(AbstractProvider $provider, int $priority): ContainerFactory
    {
        $this->providers->insert($provider, $priority);

        return $this;
    }
}
