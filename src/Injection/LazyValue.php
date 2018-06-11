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
namespace Fratily\Container\Injection;

use Fratily\Container\Resolver\Resolver;

/**
 *
 */
class LazyValue implements LazyInterface{

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @var string
     */
    protected $key;

    /**
     * Constructor
     *
     * @param   Resolver    $resolver
     * @param   string  $key
     */
    public function __construct(Resolver $resolver, string $key){
        $this->resolver = $resolver;
        $this->key      = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function load(){
        return LazyResolver::resolveLazy($this->resolver->getValue($this->key));
    }
}
