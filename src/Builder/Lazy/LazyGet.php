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

/**
 *
 */
class LazyGet extends AbstractLazy{

    /**
     * @var string|LazyInterface
     */
    private $id;

    /**
     * Constructor
     *
     * @param   string|LazyInterface    $id
     *  サービスID
     */
    public function __construct($id){
        if(!is_string($id) && !$id instanceof LazyInterface){
            throw new \InvalidArgumentException;
        }

        $this->id   = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function load(Container $container, string $expectedType = null){
        $this->lock();

        return $this->validType(
            $container->get(
                $this->id instanceof LazyInterface
                    ? $this->id->load($container, Container::T_STRING)
                    : $this->id
            ),
            $expectedType
        );
    }
}
