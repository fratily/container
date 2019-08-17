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
abstract class AbstractProvider
{
    /**
     * Define services, parameters and Injections.
     *
     * @param ContainerBuilder $builder The container builder.
     *
     * @return void
     */
    public function build(ContainerBuilder $builder): void
    {
    }

    /**
     * Modify services and parameters.
     *
     * @param Container $container The container
     *
     * @return void
     */
    public function modify(Container $container): void
    {
    }
}
