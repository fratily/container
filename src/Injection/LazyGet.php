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

use Psr\Container\{
    ContainerInterface,
    NotFoundExceptionInterface
};

/**
 *
 */
class LazyGet implements LazyInterface{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $id;

    /**
     * Constructor
     *
     * @param   ContainerInterface  $container
     * @param   string  $id
     */
    public function __construct(ContainerInterface $container, $id){
        $this->container    = $container;
        $this->id           = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function load(){
        try{
            return $this->container->get($this->id);
        }catch(NotFoundExceptionInterface $e){
            throw new \LogicException;  // TODO: Fratily/Containerの独自例外に
        }
    }
}
