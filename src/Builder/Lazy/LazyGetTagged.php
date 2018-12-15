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
class LazyGetTagged extends AbstractLazy{

    /**
     * @var string|LazyInterface
     */
    private $tag;

    /**
     * Constructor
     *
     * @param   string  $tag
     *  タグ名
     */
    public function __construct($tag){
        if(!is_string($tag) && !$tag instanceof LazyInterface){
            throw new \InvalidArgumentException;
        }

        $this->tag  = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function load(Container $container, string $expectedType = null){
        $this->lock();

        return $this->validType(
            $container->getTagged(
                $this->tag instanceof LazyInterface
                    ? $this->tag->load($container, Container::T_STRING)
                    : $this->tag
            ),
            $expectedType
        );
    }
}