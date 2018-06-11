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

/**
 *
 */
class LazyArray implements LazyInterface{

    /**
     * @var mixed[]
     */
    private $values;

    /**
     * Constructor
     *
     * @param   mixed[] $values
     */
    public function __construct(array $values){
        $this->values   = $values;
    }

    /**
     * {@inheritdoc}
     */
    public function load(){
        $this->values   = LazyResolver::resolveLazyArray($this->values);

        return $this->values;
    }
}
