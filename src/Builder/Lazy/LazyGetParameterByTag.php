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
class LazyGetParameterByTag implements LazyInterface
{
    use LockableTrait;

    /**
     * @var string|LazyInterface
     */
    private $tag;

    /**
     * Constructor.
     *
     * @param string|LazyInterface $tag The service tag name
     */
    public function __construct($tag)
    {
        if (!is_string($tag) || !(is_object($tag) && $tag instanceof LazyInterface)) {
            throw new \InvalidArgumentException();
        }

        $this->tag = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function load(Container $container): array
    {
        $this->lock();

        return $container->getParameterWithTagged(
            (is_object($this->tag) && $this->tag instanceof LazyInterface)
                ? $this->tag->load($container)
                : $this->tag
        );
    }
}
