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
class LazyPlain extends AbstractLazy{

    private $value;

    /**
     * Constructor
     *
     * @param   mixed   $callback
     *  実行するコールバック
     */
    public function __construct($value){
        $this->value    = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function load(Container $container){
        return $this->value;
    }
}