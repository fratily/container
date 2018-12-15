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
namespace Fratily\Container\Builder\Value;

use Fratily\Container\Container;
use Fratily\Container\Builder\LockableInterface;
use Fratily\Container\Builder\LockableTrait;

/**
 *
 */
abstract class AbstractValue implements LockableInterface{

    use LockableTrait;

    private $expected;
}
