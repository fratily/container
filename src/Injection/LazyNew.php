<?php
/**
 * FratilyPHP Container
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento.oka@kentoka.com>
 * @copyright   (c) Kento Oka
 * @license     MIT
 * @since       1.0.0
 */
namespace Fratily\Container\Injection;

use Fratily\Container\Resolver\Resolver;

/**
 *
 * A generic factory to create repeated instances of a single class. Note that
 * it does not implement the LazyInterface, so it is not automatically invoked
 * when resolving params and setters.
 *
 * @package Aura.Di
 *
 */
class LazyNew implements LazyInterface{

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var mixed[]
     */
    protected $params;

    /**
     * @var mixed[]
     */
    protected $setters;

    /**
     * Constructor
     *
     * @param   Resolver    $resolver
     * @param   string  $class
     * @param   mixed[] $params
     * @param   mixed[] $setters
     */
    public function __construct(
        Resolver $resolver,
        string $class,
        array $params = [],
        array $setters = []
    ) {
        $this->resolver = $resolver;
        $this->class    = $class;
        $this->params   = $params;
        $this->setters  = $setters;
    }

    /**
     * {@inheritdoc}
     */
    public function load(){
        return $this->resolver->resolve(
            $this->class,
            $this->params,
            $this->setters
        )->create();
    }
}
