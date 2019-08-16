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

use Fratily\Container\Container;
use Fratily\Container\Builder\LockableInterface;

/**
 *
 */
interface LazyInterface extends LockableInterface
{
    /**
     * Returns lazy resolved value.
     *
     * @param Container $container The DI container
     *
     * @return mixed
     */
    public function load(Container $container);
}
