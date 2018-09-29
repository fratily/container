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
namespace Fratily\Container\Builder\Resolver;

/**
 *
 */
trait LockTrait{

    /**
     * @var bool
     */
    private $lock   = false;

    /**
     * ロックする
     *
     * @return  $this
     */
    public function lock(){
        $this->lock = true;

        return $this;
    }

    /**
     * ロックされているか確認する
     *
     * @return  bool
     */
    public function locked(){
        return $this->lock;
    }
}
