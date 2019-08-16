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
class LazyGetParameter implements LazyInterface
{
    use LockableTrait;

    /**
     * @var string|LazyInterface
     */
    private $id;

    /**
     * Constructor.
     *
     * @param string|LazyInterface $id The parameter id
     */
    public function __construct($id)
    {
        if (!is_string($id) || !(is_object($id) && $id instanceof LazyInterface)) {
            throw new \InvalidArgumentException();
        }

        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function load(Container $container)
    {
        $this->lock();

        return $container->getParameter(
            (is_object($this->id) && $this->id instanceof LazyInterface)
                ? $this->id->load($container)
                : $this->id
        );
    }
}
