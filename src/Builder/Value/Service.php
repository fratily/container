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
class Service{

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string|null
     */
    private $class;

    /**
     * @var bool
     */
    private $valueOverwritable  = true;

    /**
     * @var bool
     */
    private $classOverwritable  = true;

    /**
     * @var string
     */
    private $tags   = [];

    /* インスタンス生成用の値 */

    /**
     * @var mixed[]
     */
    private $parameters = [];

    /**
     * @var string[]
     */
    private $setters    = [];

    /**
     * 値を取得する
     *
     * @return  Lazy\LazyInterface|object|null
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * 値が書き換え可能か確認する
     *
     * @return  bool
     */
    public function isOverWritableValue(){
        return $this->valueOverwritable;
    }

    /**
     * 値を設定する
     *
     * @param   Lazy\LazyInterface|object   $value
     *  値
     * @param   bool    $allowOverwrite
     *  書き換えを許可するか
     *
     * @return  $this
     */
    public function setValue($value, bool $allowOverwrite = true){
        if(!$this->isOverWritableValue()){
            throw new \LogicException;
        }

        if(!is_object($value)){
            throw new \InvalidArgumentException;
        }

        if(
            null === $this->class
            || (
                !($value instanceof Lazy\LazyInterface)
                && !is_subclass_of($value, $this->class)
            )
        ){
            $this->setClass(get_class($value));
        }

        $this->value                = $value;
        $this->valueOverwritable    = $allowOverwrite;

        return $this;
    }

    /**
     * クラス名を取得する
     *
     * @return  string|null
     */
    public function getClass(){
        return $this->class;
    }

    /**
     * クラス名が書き換え可能か確認する
     *
     * @return  bool
     */
    public function isOverWritableClass(){
        return $this->classOverwritable;
    }

    /**
     * クラス名を設定する
     *
     * @param   string  $class
     *  クラス名
     * @param   bool    $allowOverwrite
     *  書き換えを許可するか
     *
     * @return  $this
     */
    public function setClass(string $class, bool $allowOverwrite = true){
        if(!$this->isOverWritableClass()){
            throw new \LogicException;
        }

        if(!class_exists($class)){
            throw new \LogicException;
        }

        $this->class                = $class;
        $this->classOverwritable    = $allowOverwrite;

        return $this;
    }

    /**
     * タグを取得する
     *
     * @return  string[]
     */
    public function getTags(){
        return array_keys($this->tags);
    }

    /**
     * タグを追加する
     *
     * @param   string  $tag
     *  タグ名
     *
     * @return  $this
     */
    public function addTag(string $tag){
        if(1 !== preg_match(Container::REGEX_KEY, $tag)){
            throw new \InvalidArgumentException;
        }

        $this->tags[$tag]   = true;

        return $this;
    }

    public function getInjection(){
        
    }
}