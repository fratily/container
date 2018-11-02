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

/**
 *
 */
class LazyGetTaggedIdList implements LazyInterface{

    /**
     * @var string
     */
    private $tag;

    /**
     * Constructor
     *
     * @param   string  $tag
     *  タグ名
     */
    public function __construct(string $tag){
        $this->tag  = $tag;
    }

    /**
     * {@inheritdoc}
     *
     * @throw LogicException
     */
    public function load(\Fratily\Container\Container $container){
        return $container->getTaggedIdList($this->tag);
    }
}