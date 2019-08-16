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

/**
 *
 */
final class Service extends AbstractValue
{

    /**
     * {@inheritdoc}
     */
    public function set($value)
    {
        if (is_string($value)) {
            if (!class_exists($value)) {
                throw new \InvalidArgumentException();
            }

            $value  = new Lazy\LazyNew($value);
        }

        if (!is_object($value)) {
            throw new \InvalidArgumentException();
        }

        return parent::set($value);
    }

    /**
     * {@inheritdoc}
     */
    public function setType(string $type, bool $overwritable = false)
    {
        if (!class_exists($type) && !interface_exists($type)) {
            throw new \InvalidArgumentException();
        }

        parent::setType($type, $overwritable);
    }
}
