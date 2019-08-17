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

use Fratily\Container\Builder\LockableTrait;
use Fratily\Container\Container;

/**
 *
 */
class LazyNew implements LazyInterface
{
    use LockableTrait;

    /**
     * @var string|LazyInterface
     */
    private $class;

    /**
     * Constructor.
     *
     * @param string|LazyInterface $class The class name
     */
    public function __construct($class)
    {
        if (!is_string($class) || !(is_object($class) && $class instanceof LazyInterface)) {
            throw new \InvalidArgumentException();
        }

        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function load(Container $container): object
    {
        $this->lock();

        return $container->new(
            (is_object($this->class) && $this->class instanceof LazyInterface)
                ? $this->class->load($container)
                : $this->class
        );
    }
}
