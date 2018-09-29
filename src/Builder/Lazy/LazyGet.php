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
class LazyGet implements LazyInterface{

    /**
     * @var string
     */
    private $id;

    /**
     * Constructor
     *
     * @param   string  $id
     *  サービスID
     */
    public function __construct(string $id){
        $this->id   = $id;
    }

    /**
     * @inheritdoc
     *
     * @return  object
     */
    public function load(\Fratily\Container\Container $container){
        return $container->get($this->id);
    }
}
