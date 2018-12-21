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
namespace Fratily\Container\Builder\Value\Lazy;

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
        if(!is_string($id) && !$this->isLazyObject($id)){
            throw new \InvalidArgumentException;
        }

        $this->id   = $id;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadValue(Container $container){
        return $container->get(
            $this->isLazyObject($this->id)
                ? $this->id->load($container, "string")
                : $this->id
        );
    }
}
