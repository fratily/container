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
class ContainerBuilder{

    use LazyBuilderTrait;

    /**
     * @var Resolver\Resolver
     */
    private $resolver;

    /**
     * @var object[]|Lazy\LazyInterface[]
     */
    private $services       = [];

    /**
     * @var string[]
     */
    private $taggedServices = [];

    /**
     * Constructor
     *
     * @param   Resolver\Resolver   $resolver
     *  リゾルバ
     */
    public function __construct(Resolver\Resolver $resolver){
        $this->resolver = $resolver;
    }

    /**
     * リゾルバを取得する
     *
     * @return  Resolver\Resolver
     */
    public function getResolver(){
        return $this->resolver;
    }

    /**
     * サービスのリストを取得する
     *
     * @return  object[]|Lazy\LazyInterface[]
     */
    public function getServices(){
        return $this->services;
    }

    /**
     * タグ付けがされたサービスIDのリストを取得する
     *
     * @return  string[][]
     */
    public function getTaggedServicesId(){
        return $this->taggedServices;
    }

    /**
     * サービスを登録する
     *
     * @param   string  $id
     *  サービスID
     * @param   string|LazyInterface|object $service
     *  サービス
     * @param   string[]    $tags
     *  サービスにつけるタグの配列
     * @param   string[]    $types
     *  パラメータの型指定による自動解決時に、
     *  このサービスがどのようなクラス指定に使用されるかを示す配列
     *
     * @return  $this
     */
    public function add(
        string $id,
        $service,
        array $tags = [],
        array $types = []
    ){
        if(!is_string($service) && !is_object($service)){
            throw new \InvalidArgumentException();
        }

        if(is_string($service)){
            if(!class_exists($service)){
                throw new \InvalidArgumentException();
            }

            if(!$this->resolver->getClassResolver($service)->getReflection()->isInstantiable()){
                throw new \InvalidArgumentException();
            }

            $service    = new Lazy\LazyNew($service);
        }

        $this->services[$id]    = $service;

        foreach($tags as $tag){
            if(!array_key_exists($tag, $this->taggedServices)){
                $this->taggedServices[$tag] = [];
            }

            $this->taggedServices[$tag][]   = $id;
        }

        foreach($types as $type){
            $this->resolver->addType($type, new Lazy\LazyGet($id));
        }

        return $this;
    }

    /**
     * クラスのインスタンス化モードをシングルトンにする
     *
     * @param   string  $class
     *  クラス名
     *
     * @return  $this
     */
    public function isSingleton(string $class){
        $this->resolver->getClassResolver($class)
            ->getInstanceGenerator()
            ->setIsSingleton(true)
        ;

        return $this;
    }

    /**
     * クラスのインスタンス化モードをプロトタイプにする
     *
     * @param   string  $class
     *  クラス名
     *
     * @return  $this
     */
    public function isPrototype(string $class){
        $this->resolver->getClassResolver($class)
            ->getInstanceGenerator()
            ->setIsSingleton(false)
        ;

        return $this;
    }

    /**
     *  パラメーターを登録する
     *
     * @param   string  $class
     *  クラス名
     * @param   int|string  $parameter
     *  パラメーター名もしくはパラメーターポジション
     * @param   mixed   $value
     *  インジェクションする値
     *
     * @return  $this
     */
    public function addParameter(string $class, $parameter, $value){
        if(!is_string($parameter) && !is_int($parameter)){
            throw new \InvalidArgumentException();
        }

        if(is_string($parameter)){
            if("" === $parameter){
                throw new \InvalidArgumentException();
            }

            $this->resolver
                ->getClassResolver($class)
                ->addNameParameter($parameter, $value)
            ;
        }else{
            if(0 > $parameter){
                throw new \InvalidArgumentException();
            }

            $this->resolver
                ->getClassResolver($class)
                ->addPositionParameter($parameter, $value)
            ;
        }

        return $this;
    }

    /**
     * セッターを登録する
     *
     * @param   string  $class
     *  クラス名
     * @param   string  $setter
     *  メソッド名
     * @param type $value
     *  インジェクションする値
     *
     * @return  $this
     */
    public function addSetter(string $class, string $setter, $value){
        $this->resolver->getClassResolver($class)->addSetter($setter, $value);

        return $this;
    }

    /**
     * プロパティを登録する
     *
     * @param   string  $class
     *  クラス名
     * @param   string  $property
     *  プロパティ名
     * @param   mixed   $value
     *  インジェクションする値
     *
     * @return  $this
     */
    public function addProperty(string $class, string $property, $value){
        $this->resolver->getClassResolver($class)->addProperty($property, $value);

        return $this;
    }

    /**
     * パラメータ登録のオブジェクティブ版を取得する
     *
     * @param   string  $class
     *  クラス名
     *
     * @return  ParameterBuilder
     */
    public function parameter(string $class){
        return new ParameterBuilder($this, $class);
    }

    /**
     * セッター登録のオブジェクティブ版を取得する
     *
     * @param   string  $class
     *  クラス名
     *
     * @return  SetterBuilder
     */
    public function setter(string $class){
        return new SetterBuilder($this, $class);
    }

    /**
     * プロパティ登録のオブジェクティブ版を取得する
     *
     * @param   string  $class
     *  クラス名
     *
     * @return  PropertyBuilder
     */
    public function property(string $class){
        return new PropertyBuilder($this, $class);
    }

    /**
     * サービスコンテナ内共有値を登録する
     *
     * @param   string  $name
     *  共有値名
     * @param   mixed   $value
     *  値
     *
     * @return  $this
     */
    public function addShareValue(string $name, $value){
        $this->resolver->addShareValue($name, $value);

        return $this;
    }
}