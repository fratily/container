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

use Fratily\Container\Injection;
use Fratily\Reflection\Reflector\ClassReflector;

/**
 *
 */
class Resolver{

    /**
     * @var mixed[]
     */
    private $params     = [];

    /**
     * @var mixed[]
     */
    private $setters    = [];

    /**
     * @var mixed[]
     */
    private $values     = [];

    /**
     * @var mixed[]
     */
    private $types      = [];

    /**
     * @var mixed[]
     */
    private $unified    = [];

    /**
     * @var ClassReflector
     */
    private $reflector;

    /**
     * @var bool
     */
    private $effectiveAuto;

    /**
     * Constructor
     *
     * @param   ClassReflector  $reflector
     * @param   bool    $auto
     */
    public function __construct(ClassReflector $reflector, bool $auto = false){
        $this->reflector        = $reflector;
        $this->effectiveAuto    = $auto;
    }

    /**
     * コンストラクタインジェクションの値を取得
     *
     * @param   string  $class
     * @param   string|int  $name
     *
     * @return  mixed|null
     *  もし存在しなければnullが返る。値にnullを指定しているのか確認するには
     *  hasParameter()メソッドを利用する。
     */
    public function getParameter(string $class, $name){
        return $this->params[$class][$name] ?? null;
    }

    /**
     * コンストラクタインジェクションの値設定が存在するか
     *
     * @param   string  $class
     * @param   string|int  $name
     *
     * @return  bool
     */
    public function hasParameter(string $class, string $name){
        return array_key_exists($class, $this->params)
            && array_key_exists($name, $this->params[$class]);
    }

    /**
     * コンストラクタインジェクションの値を追加
     *
     * @param   string  $class
   　* @param   string|int  $name
     * @param   mixed   $value
     *
     * @return  $this
     */
    public function addParameter(string $class, $name, $value){
        if(!class_exists($class) && !trait_exists($class) && !interface_exists($class)){
            throw new \InvalidArgumentException();
        }

        if(!is_string($name) && !is_int($name)){
            throw new \InvalidArgumentException();
        }

        if(!array_key_exists($class, $this->params)){
            $this->params[$class]   = [];
        }

        $this->params[$class][$name]    = $value;

        return $this;
    }

    /**
     * セッターインジェクションの値を取得
     *
     * @param   string  $class
     * @param   string  $method
     *
     * @return  mixed|null
     *      もし存在しなければnullが返る。明示的にnullを指定しているのか
     *      確認するには、hasSetter()メソッドを利用する。
     */
    public function getSetter(string $class, string $method){
        return $this->setters[$class][$method] ?? null;
    }

    /**
     * セッターとその値の連想配列を取得
     *
     * @param   string  $class
     *
     * @return  mixed[]
     */
    public function getSetters(string $class){
        return $this->setters[$class] ?? [];
    }

    /**
     * セッターインジェクションの値設定が存在するか
     *
     * @param   string  $class
     * @param   string  $method
     *
     * @return  bool
     */
    public function hasSetter(string $class, string $method){
        return array_key_exists($class, $this->setters)
            && array_key_exists($method, $this->setters[$class]);
    }

    /**
     * セッターインジェクションの値を追加
     *
     * @param   string  $class
   　* @param   string  $method
     * @param   mixed   $value
     *
     * @return  $this
     */
    public function addSetter(string $class, string $method, $value){
        if(!class_exists($class) && !trait_exists($class) && !interface_exists($class)){
            throw new \InvalidArgumentException();
        }

        if($method === ""){
            throw new \InvalidArgumentException();
        }

// __callでセッターを定義している可能性もある
//        if(!method_exists($class, $method)){
//            $method = "set" . ucfirst($method);
//
//            if(!method_exists($class, $method)){
//                throw new \InvalidArgumentException();
//            }
//        }

        if(!array_key_exists($class, $this->setters)){
            $this->setters[$class]  = [];
        }

        $this->setters[$class][$method] = $value;

        return $this;
    }

    /**
     * コンストラクタインジェクションにおける自動解決用の値を取得
     *
     * @param   string  $class
     *
     * @return  mixed|null
     *      もし存在しなければnullが返る。明示的にnullを指定しているのか
     *      確認するには、hasSetter()メソッドを利用する。
     */
    public function getType(string $class){
        return $this->types[$class] ?? null;
    }

    /**
     * コンストラクタインジェクションにおける自動解決用の値が存在するか確認
     *
     * @param   string  $class
     *
     * @return  bool
     */
    public function hasType(string $class){
        return array_key_exists($class, $this->types);
    }

    /**
     * コンストラクタインジェクションにおける自動解決用の値を追加
     *
     * @param   string  $class
     * @param   object|Injection\LazyInterface  $instance
     *
     * @return  $this
     */
    public function addType(string $class, $instance){
        if(!class_exists($class) && !interface_exists($class)){
            throw new \InvalidArgumentException();
        }

        if(!is_object($instance)){
            throw new \InvalidArgumentException();
        }

        $this->types[$class]    = $instance;

        return $this;
    }

    /**
     * 値を取得
     *
     * @param   $name
     *
     * @return  mixed|null
     *      もし存在しなければnullが返る。明示的にnullを指定しているのか
     *      確認するには、hasValue()メソッドを利用する。
     */
    public function getValue(string $name){
        return $this->values[$name] ?? null;
    }

    /**
     * 値設定が存在するか
     *
     * @param   string  $name
     *
     * @return  bool
     */
    public function hasValue(string $name){
        return array_key_exists($name, $this->values);
    }

    /**
     * 値を追加
     *
     * @param   string  $name
     * @param   mixed   $value
     *
     * @return  $this
     */
    public function addValue(string $name, $value){
        $this->values[$name]    = $value;

        return $this;
    }

    /**
     * インスタンス生成用オブジェクトを生成する
     *
     * @param   string  $class
     * @param   mixed[] $params
     * @param   mixed[] $setters
     *
     * @return  ResolveClass
     */
    public function resolve(
        string $class,
        array $params = [],
        array $setters = []
    ){
        if(!class_exists($class)){
            throw new \InvalidArgumentException();
        }

        $unified    = $this->getUnified($class);

        return new ResolveClass(
            $this->reflector->getClass($class),
            $this->mergeParams($class, $unified["params"], $params),
            $this->mergeSetters($class, $unified["setters"], $setters)
        );
    }

    /**
     * セッターメソッドリストをマージする
     *
     * @param   string  $class
     * @param   mixed[] $setters
     * @param   mixed[] $mergeSetters
     *
     * @return  mixed[]
     *
     */
    protected function mergeSetters(
        string $class,
        array $setters,
        array $mergeSetters
    ){
        return array_merge($setters, $mergeSetters);
    }

    /**
     * パラメータリストをマージする
     *
     * @param   string  $class
     * @param   mixed[] $params
     * @param   mixed[] $mergeParams
     *
     * @return  mixed[]
     */
    protected function mergeParams(
        string $class,
        array $params,
        array $mergeParams
    ){
        foreach($this->reflector->getParameters($class) as $param){
            if(array_key_exists($param->getPosition(), $mergeParams)){
                $params[$param->getPosition()]  = $mergeParams[$param->getPosition()];
            }else if(array_key_exists($param->getName(), $mergeParams)){
                $params[$param->getPosition()]  = $mergeParams[$param->getName()];
            }
        }

        return $params;
    }

    /**
     * 親などが考慮されたパラメータとセッターのリストを取得する
     *
     * @param   string  $class
     *
     * @return  mixed[][]
     *
     */
    public function getUnified(string $class){
        if(!array_key_exists($class, $this->unified)){
            $spec = [
                "params"    => [],
                "setters"   => []
            ];

            if(($parent = get_parent_class($class)) !== false){
                $spec   = $this->getUnified($parent);
            }

            $this->unified[$class]  = [
                "params"    => $this->getUnifiedParams($class, $spec["params"]),
                "setters"   => $this->getUnifiedSetters($class, $spec["setters"])
            ];
        }

        return $this->unified[$class];
    }

    /**
     * 親を考慮したパラメータリストを取得する
     *
     * @param   string  $class
     * @param   mixed[] $parent
     *
     * @return  mixed[]
     *
     */
    protected function getUnifiedParams(string $class, array $parent){
        $result     = [];
        $interfaces = class_implements($class);
        $traits     = $this->reflector->getTraits($class);

        foreach($this->reflector->getParameters($class) as $param){
            $offset = null;
            $value  = null;

            // パラメータ位置指定
            if($this->hasParameter($class, $param->getPosition())){
                $offset = $param->getPosition();
                $value  = $this->getParameter($class, $param->getPosition());
            }

            // パラメータ名指定
            if($offset === null && $this->hasParameter($class, $param->getName())){
                $offset = $param->getName();
                $value  = $this->getParameter($class, $param->getName());
            }

            foreach($interfaces as $interface){
                if($offset === null && $this->hasParameter($interface, $param->getName())){
                    $offset = $param->getName();
                    $value  = $this->getParameter($interface, $param->getName());

                    break;
                }
            }

            foreach($traits as $trait){
                if($offset === null && $this->hasParameter($trait, $param->getName())){
                    $offset = $param->getName();
                    $value  = $this->getParameter($trait, $param->getName());

                    break;
                }
            }

            if($offset === null && array_key_exists($param->getName(), $parent)){
                $offset = $param->getName();
                $value  = $parent[$param->getName()];
            }

            if($offset === null && $this->effectiveAuto){
                $type   = $param->getClass();

                if($type !== null && $this->hasType($type->getName())){
                    $offset = $param->getPosition();
                    $value  = $this->getType($type->getName());
                }
            }

            if($offset === null && $param->isDefaultValueAvailable()){
                $offset = $param->getPosition();
                $value  = $param->getDefaultValue();
            }

            if($offset === null){
                $offset = $param->getPosition();
                $value  = null;
            }

            $result[$offset]    = $value;
        }

        return $result;
    }

    /**
     * 親を考慮したセッターリストの取得
     *
     * @param   string  $class
     * @param   mixed[] $parent
     *
     * @return  mixed[]
     */
    protected function getUnifiedSetters(string $class, array $parent){
        $unified    = $parent;
        $interfaces = class_implements($class);
        $traits     = $this->reflector->getTraits($class);

        foreach($interfaces as $interface){
            $unified = array_merge(
                $unified,
                $this->getSetters($interface)
            );
        }

        foreach($traits as $trait){
            $unified = array_merge(
                $unified,
                $this->getSetters($trait)
            );
        }

        $unified = array_merge(
            $unified,
            $this->getSetters($class)
        );

        return $unified;
    }

    /**
     * パラメータリストから引数リストを作成する
     *
     * @param   \ReflectionParameter[]  $params
     * @param   mixed[] $data
     *
     * @return  mixed[]
     */
    public function resolveParameters(array $params, array $data){
        $result     = [];

        foreach($params as $param){
            if(array_key_exists($param->getPosition(), $data)){
                $result[]   = $data[$param->getPosition()];
            }else if(array_key_exists($param->getName(), $data)){
                $result[]   = $data[$param->getName()];
            }else{
                $add    = false;
                $class  = $param->getClass();

                if($class !== null && $this->effectiveAuto){
                    if($this->hasType($class->getName())){
                        $add        = true;
                        $result[]   = $this->getType($class->getName());
                    }else if($class->isInstantiable()){
                        $add        = true;
                        $result[]   = new LazyNew($this, $class->getName());
                    }
                }

                if($param->isDefaultValueAvailable()){
                    $add        = true;
                    $result[]   = $param->getDefaultValue();
                }

                if(!$add){
                    $result[]   = null;
                }
            }
        }

        return $result;
    }
}
