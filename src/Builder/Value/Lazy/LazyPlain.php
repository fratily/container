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
use Fratily\Container\Builder\Exception\LockedException;

/**
 *
 */
class LazyPlain extends AbstractLazy{

    /**
     * @var mixed
     */
    private $value;

    /**
     * {@inheritdoc}
     */
    public function loadValue(Container $container){
        return $this->value;
    }

    /**
     * 値を設定する
     * @param   mixed   $value
     *  値
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function value($value){
        if($this->isLocked()){
            throw new LockedException();
        }

        $this->value    = $value;

        return $this;
    }
}