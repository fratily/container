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
namespace Fratily\Container\Builder\Value;

use Fratily\Container\Builder\Exception\LockedException;
use Fratily\Container\Builder\Lazy\LazyInterface;
use Fratily\Container\Builder\Lazy\LazyNew;

/**
 *
 */
final class Service extends AbstractValue{

    /**
     * @var string[]
     */
    private $setters    = [];

    /**
     * {@inheritdoc}
     */
    public function set($value){
        if(is_string($value)){
            if(!class_exists($value)){
                throw new \InvalidArgumentException;
            }

            $value  = new LazyNew($class);
        }

        if(!is_object($value)){
            throw new \InvalidArgumentException;
        }

        return parent::set($value);
    }

    /**
     * {@inheritdoc}
     */
    public function setType(string $type, bool $overwritable = false){
        if(!class_exists($type) && !interface_exists($type)){
            throw new \InvalidArgumentException;
        }

        parent::setType($type, $overwritable);
    }

    /**
     * セッターのリストを取得する
     *
     * @return  array[]
     */
    public function getSetters(){
        return $this->setters;
    }

    /**
     * セッターを追加する
     *
     * @param   string  $method
     *  メソッド名
     * @param   mixed   ...$args
     *  セッター実行時に渡す引数
     */
    public function addSetter(string $method, ...$args){
        if($this->isLocked()){
            throw new LockedException();
        }

        $this->setters[$method] = $args;

        return $this;
    }
}