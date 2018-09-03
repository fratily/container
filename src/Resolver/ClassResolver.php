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
namespace Fratily\Container\Resolver;

/**
 *
 */
class ClassResolver{

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var \ReflectionClass
     */
    private $reflection;

    /**
     * @var mixed[]
     */
    private $posParameters      = [];

    /**
     * @var mixed[]
     */
    private $nameParameters     = [];

    /**
     * @var mixed[]
     */
    private $properties         = [];

    /**
     * @var mixed[]
     */
    private $privateProperties  = [];

    /**
     * @var mixed[]
     */
    private $setters            = [];

    /**
     * @var mixed[]|null
     */
    private $unifiedParameters      = null;

    /**
     * @var mixed[]|null
     */
    private $unifiedProperties      = null;

    /**
     * @var mixed[]|null
     */
    private $unifiedSetters         = null;

    private $instanceGenerator      = null;

    /**
     * Constructor
     *
     * @param   Resolver    $resolver
     *  依存関係を統括するリゾルバオブジェクト
     * @param   \ReflectionClass    $reflection
     *  依存を定義する対象クラスのリフレクションオブジェクト
     */
    public function __construct(Resolver $resolver, \ReflectionClass $reflection){
        $this->resolver     = $resolver;
        $this->reflection   = $reflection;
    }

    /**
     * インスタンスジェネレータを生成する
     *
     * @return  InstanceGenerator
     */
    public function createInstanceGenerator(){
        if(null === $this->instanceGenerator){
            $this->instanceGenerator    = new InstanceGenerator(
                $this->resolver,
                $this->reflection->getName(),
                InstanceGenerator::SINGLETON
            );
        }

        return $this->instanceGenerator;
    }

    /**
     * リフレクションを取得する
     *
     * @return  \ReflectionClass
     */
    public function getReflection(){
        return $this->reflection;
    }

    /**
     * 指定したポジションのパラメータの値を取得する
     *
     * @param   int $pos
     *  パラメータポジション
     *
     * @return  mixed|null
     *  もし存在しなければnullが返る。値にnullを指定しているのか確認するには
     *  hasParameter()メソッドを利用する。
     */
    public function getPositionParameter(int $pos){
        return $this->posParameters[$pos] ?? null;
    }

    /**
     * 指定した名前のパラメータの値を取得する
     *
     * @param   string  $name
     *  パラメータ名
     *
     * @return  mixed|null
     *  もし存在しなければnullが返る。値にnullを指定しているのか確認するには
     *  hasParameter()メソッドを利用する。
     */
    public function getNameParameter(string $name){
        return $this->nameParameters[$name] ?? null;
    }

    /**
     * パラメータポジションとその値の連想配列を取得する
     *
     * @return  mixed[]
     */
    public function getPostionParameters(){
        return $this->posParameters;
    }

    /**
     * パラメータ名とその値の連想配列を取得する
     *
     * @return  mixed[]
     */
    public function getNameParameters(){
        return $this->nameParameters;
    }

    /**
     * 指定したポジションのパラメータの値が登録済みか確認する
     *
     * @param   int $pos
     *  パラメータポジション
     *
     * @return  bool
     */
    public function hasPositionParameter(int $pos){
        return array_key_exists($pos, $this->posParameters);
    }

    /**
     * 指定した名前のパラメータの値が登録済みか確認する
     *
     * @param   string  $name
     *  パラメータ名
     *
     * @return  bool
     */
    public function hasNameParameter(string $name){
        return array_key_exists($name, $this->nameParameters);
    }

    /**
     * 指定ポジションのパラメータにインジェクションする値を登録する
     *
     * @param   int $pos
     *  パラメータポジション
     * @param   mixed   $value
     *  インジェクションする値
     *
     * @return  $this
     *
     * @throws  \InvalidArgumentException
     */
    public function addPositionParameter(int $pos, $value){
        if($pos < 0){
            throw new \InvalidArgumentException();
        }

        $this->posParameters[$pos]  = $value;

        return $this;
    }

    /**
     * 指定名のパラメータにインジェクションする値を登録する
     *
     * @param   string  $name
     *  パラメータ名
     * @param   mixed   $value
     *  インジェクションする値
     *
     * @return  $this
     */
    public function addNameParameter(string $name, $value){
        $this->nameParameters[$name]    = $value;

        return $this;
    }

    /**
     * 指定したセッターメソッドでインジェクションする値を取得する
     *
     * @param   string  $method
     *  セッターメソッド名
     *
     * @return  mixed|null
     *  もし存在しなければnullが返る。明示的にnullを指定しているのか
     *  確認するには、hasSetter()メソッドを利用する。
     */
    public function getSetter(string $method){
        return $this->setters[$method] ?? null;
    }

    /**
     * セッターメソッド名とインジェクションする値の連想配列を取得する
     *
     * @return  mixed[]
     */
    public function getSetters(){
        return $this->setters;
    }

    /**
     * 指定した名前のセッターメソッドが登録済みか確認する
     *
     * @param   string  $method
     *  セッターメソッド名
     *
     * @return  bool
     */
    public function hasSetter(string $method){
        return array_key_exists($method, $this->setters);
    }

    /**
     * 指定したセッターメソッドにインジェクションする値を登録する
     *
     * マジックメソッドを活用してセッターを作成している場合はインジェクションできません。
     *
   　* @param   string  $name
     *  セッターメソッド名
     * @param   mixed   $value
     *  インジェクションする値
     *
     * @return  $this
     *
     * @throws  \InvalidArgumentException
     */
    public function addSetter(string $name, $value){
        if(
            !$this->reflection->hasMethod($name)
//            && !$this->reflection->hasMethod("__call")
        ){
            throw new \InvalidArgumentException();
        }

        if(
            $this->reflection->getMethod($name)->isStatic()
            || !$this->reflection->getMethod($name)->isPublic()
        ){
            throw new \InvalidArgumentException();
        }

        $this->setters[$name] = $value;

        return $this;
    }

    /**
     * 指定したプロパティにインジェクションする値を取得する
     *
     * @param   string  $name
     *  プロパティ名
     *
     * @return  mixed|null
     *  もし存在しなければnullが返る。明示的にnullを指定しているのか
     *  確認するには、hasSetter()メソッドを利用する。
     */
    public function getProperty(string $name){
        return $this->properties[$name] ?? $this->privateProperties[$name] ?? null;
    }

    /**
     * プロパティ名とインジェクションする値の連想配列を取得する
     *
     * @return  mixed[]
     */
    public function getProperties(){
        return $this->properties;
    }

    /**
     * 指定した名前のプロパティが登録済みか確認する
     *
     * @param   string  $name
     *  プロパティ名
     *
     * @return  bool
     */
    public function hasProperty(string $name){
        return array_key_exists($name, $this->properties)
            || array_key_exists($name, $this->privateProperties)
        ;
    }

    /**
     * 指定したプロパティにインジェクションする値を登録する
     *
     * マジックメソッドを活用してプロパティを作成している場合はインジェクションできません。
     *
   　* @param   string  $name
     *  プロパティ名
     * @param   mixed   $value
     *  インジェクションする値
     *
     * @return  $this
     *
     * @throws  \InvalidArgumentException
     */
    public function addProperty(string $name, $value){
        if(
            !$this->reflection->hasProperty($name)
//            && !$this->reflection->hasMethod("__set")
        ){
            throw new \InvalidArgumentException();
        }

        if($this->reflection->getProperty($name)->isStatic()){
            throw new \InvalidArgumentException();
        }

        if($this->reflection->getProperty($name)->isPrivate()){
            $this->properties[$name]    = $value;
        }else{
            $this->privateProperties[$name] = $value;
        }

        return $this;
    }

    /**
     * 継承したクラスやインターフェース等を考慮したプロパティ名とその値の連想配列を取得する
     *
     * @param   bool    $considerInterface
     *  実装してるインターフェースを考慮するか。
     *
     * @return  mixed[]
     */
    public function getUnifiedParameters(bool $considerInterface = true){
        if(null === $this->unifiedParameters){
            $this->unifiedParameters    = [];

            if(false !== ($parent = $this->reflection->getParentClass())){
                $this->unifiedParameters    = array_merge(
                    $this->unifiedParameters,
                    $this->resolver
                        ->getClassResolver($parent->getName())
                        ->getUnifiedParameters(false)
                );
            }

            if($considerInterface){
                foreach($this->reflection->getInterfaceNames() as $interface){
                    $this->unifiedParameters    = array_merge(
                    $this->unifiedParameters,
                        $this->resolver
                            ->getClassResolver($interface)
                            ->getUnifiedParameters(false)
                    );
                }
            }

            foreach($this->reflection->getTraitNames() as $trait){
                $this->unifiedParameters    = array_merge(
                    $this->unifiedParameters,
                    $this->resolver
                        ->getClassResolver($trait)
                        ->getUnifiedParameters(false)
                );
            }

            $this->unifiedParameters    = array_merge(
                $this->unifiedParameters,
                $this->nameParameters
            );
        }

        return $this->unifiedParameters;
    }

    /**
     * 継承したクラスやインターフェース等を考慮したセッターとその値の連想配列を取得する
     *
     * @param   bool    $considerInterface
     *  実装してるインターフェースを考慮するか。
     *
     * @return  mixed[]
     */
    public function getUnifiedSetters(bool $considerInterface = true){
        if(null === $this->unifiedSetters){
            $this->unifiedSetters   = [];

            if(false !== ($parent = $this->reflection->getParentClass())){
                $this->unifiedSetters   = array_merge(
                    $this->unifiedSetters,
                    $this->resolver
                        ->getClassResolver($parent->getName())
                        ->getUnifiedSetters(false)
                );
            }

            if($considerInterface){
                foreach($this->reflection->getInterfaceNames() as $interface){
                    $this->unifiedSetters   = array_merge(
                    $this->unifiedSetters,
                        $this->resolver
                            ->getClassResolver($interface)
                            ->getUnifiedSetters(false)
                    );
                }
            }

            foreach($this->reflection->getTraitNames() as $trait){
                $this->unifiedSetters   = array_merge(
                    $this->unifiedSetters,
                    $this->resolver
                        ->getClassResolver($trait)
                        ->getUnifiedSetters(false)
                );
            }

            $this->unifiedSetters   = array_merge(
                $this->unifiedSetters,
                $this->setters
            );
        }

        return $this->unifiedSetters;
    }

    /**
     * 継承したクラス等を考慮したプロパティとその値の連想配列を取得する
     *
     * @return  mixed[]
     */
    public function getUnifiedProperties(){
        if(null === $this->unifiedProperties){
            $this->unifiedProperties    = [];

            if(false !== ($parent = $this->reflection->getParentClass())){
                $this->unifiedProperties    = array_merge(
                    $this->unifiedProperties,
                    $this->resolver
                        ->getClassResolver($parent->getName())
                        ->getUnifiedProperties()
                );
            }

            foreach($this->reflection->getTraitNames() as $trait){
                $this->unifiedProperties    = array_merge(
                    $this->unifiedProperties,
                    $this->resolver
                        ->getClassResolver($trait)
                        ->getUnifiedProperties()
                );
            }

            $this->unifiedProperties    = array_merge(
                $this->unifiedProperties,
                $this->properties
            );
        }

        return $this->unifiedProperties;
    }
}
