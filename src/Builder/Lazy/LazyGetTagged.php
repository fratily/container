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
class LazyGetTagged implements LazyInterface{

    /**
     * @var string
     */
    private $tag;

    /**
     * Constructor
     *
     * @param   mixed   $callback
     *  実行するコールバック
     * @param   mixed[] $parameters
     *  追加指定パラメータの配列
     * @param   mixed[] $types
     *  追加指定型指定解決値の配列
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
        return $container->getTagged($this->tag);
    }
}