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
interface LockableInterface
{
    /**
     * Lock an instance.
     *
     * @return void
     */
    public function lock(): void;

    /**
     * Returns if the instance is locked.
     *
     * @return bool
     */
    public function isLocked(): bool;
}
