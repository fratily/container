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
class LazyArray implements LazyInterface
{
    use LockableTrait;

    /**
     * @var array
     */
    private $value;

    /**
     * Constructor.
     *
     * @param mixed[] $value The value
     */
    public function __construct(array $value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function load(Container $container): array
    {
        $this->lock();

        return array_map(
            function ($value) use ($container) {
                return (is_object($value) && $value instanceof LazyInterface)
                    ? $value->load($container)
                    : $value
                ;
            },
            $this->value
        );
    }
}
