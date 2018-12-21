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
use Fratily\Container\Builder\LockableTrait;
use Fratily\Container\Builder\Exception\LockedException;

abstract class AbstractValue implements ValueInterface{

    use LockableTrait;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed[]
     */
    private $tags   = [];

    /**
     * インスタンス生成から値登録までのショートカット
     *
     * @param   mixed   $value
     *  値
     * @param   string  $type
     *  型
     *
     * @return type
     */
    public static function create($value, string $type = "mixed"){
        return (new static($type))->set($value);
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(string $type = "mixed"){
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function get(){
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function set($value){
        if($this->isLocked()){
            throw new LockedException();
        }

        $this->value    = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(){
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags(){
        return array_keys($this->tags);
    }

    /**
     * {@inheritdoc}
     */
    public function addTag(string $tag){
        if($this->isLocked()){
            throw new LockedException();
        }

        if(1 !== preg_match(Container::REGEX_KEY, $tag)){
            throw new \InvalidArgumentException;
        }

        $this->tags[$tag]   = true;

        return $this;
    }
}