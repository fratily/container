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

use Fratily\Container\Container;

/**
 *
 */
final class Service extends AbstractValue{

    /**
     * @var string[]
     */
    private $aliases    = [];

    /**
     * {@inheritdoc}
     */
    public function set($value){
        if(is_string($value)){
            if(!class_exists($value)){
                throw new \InvalidArgumentException;
            }

            $value  = new Lazy\LazyNew($class);
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
     * サービスの別名のリストを取得する
     *
     * @return  string[]
     */
    public function getAliases(){
        return array_keys($this->aliases);
    }

    /**
     * サービスの別名を追加する
     *
     * @param   string  $alias
     *  別名
     * @param   string  $id
     *  サービスID
     *
     * @return  $this
     */
    public function addAlias(string $alias){
        if(1 !== preg_match(Container::REGEX_KEY, $alias)){
            throw new \InvalidArgumentException;
        }

        $this->aliases[$alias]  = true;

        return $this;
    }
}