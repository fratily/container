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
namespace Fratily\Container\Builder;

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
    private $instanceOf;

    /**
     * @var string
     */
    private $tags   = [];

    /**
     * オブジェクトからサービスを生成する
     *
     * @param   object  $object
     *  サービスの値
     *
     * @return  static
     */
    public static function object($object){
        if(!is_object($object)){
            throw new \InvalidArgumentException;
        }

        if($object instanceof Lazy\LazyInterface){
            throw new \InvalidArgumentException;
        }

        return new static($object, get_class($object));
    }

    /**
     * オブジェクトからサービスを生成する
     *
     * @param   Lazy\LazyInterface  $lazy
     *  サービスの値を遅延取得インスタンス
     * @param   string|null $instanceOf
     *  このサービスのクラス名
     *
     * @return  static
     */
    public static function lazy(Lazy\LazyInterface $lazy, string $instanceOf){
        return new static($lazy, $instanceOf);
    }

    /**
     * Constructor
     *
     * @param   Lazy\LazyInterface|object   $value
     *  サービスの値
     * @param   string|null $instanceOf
     *  このサービスのクラス名
     */
    public function __construct($value, string $instanceOf = null){
        if(!is_object($value)){
            throw new \InvalidArgumentException;
        }

        if(null !== $instanceOf && !class_exists($instanceOf)){
            throw new \InvalidArgumentException;
        }

        $this->value        = $value;
        $this->instanceOf   = $instanceOf;
    }

    /**
     * サービスの値を取得する
     *
     * @return  Lazy\LazyInterface|object
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * サービスのクラス名を取得する
     *
     * @return  string|null
     */
    public function getInstanceOf(){
        return $this->instanceOf;
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
        if(1 !== preg_match(self::REGEX_TAG, $tag)){
            throw new \InvalidArgumentException;
        }

        $this->tags[$tag]   = true;

        return $this;
    }
}