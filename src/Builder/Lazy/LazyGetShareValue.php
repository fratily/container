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
class LazyGetShareValue implements LazyInterface{

    /**
     * @var string
     */
    private $name;

    /**
     * Constructor
     *
     * @param   string  $name
     *  共有値名
     */
    public function __construct(string $name){
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function load(\Fratily\Container\Container $container){
        return $container->getResolver()->getShareValue($name);
    }
}