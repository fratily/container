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
namespace Fratily\Container\Builder\Lazy;

use Fratily\Container\Container;

/**
 *
 */
class LazyNew extends AbstractLazy{

    /**
     * @var string
     */
    private $class;

    /**
     * @var mixed[]
     */
    private $parameters;

    /**
     * @var mixed[]
     */
    private $types;

    /**
     * Constructor
     *
     * @param   string  $class
     *  クラス名
     */
    public function __construct($class){
        if(
            !is_string($class)
            && !(is_object($class) && $class instanceof LazyInterface)
        ){
            throw new \InvalidArgumentException;
        }

        $this->class    = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function load(Container $container, string $expectedType = null){
        $this->lock();

        $class  = $this->class instanceof LazyInterface
            ? $this->class->load($container, Container::T_STRING)
            : $this->class
        ;

        if(!is_string($class)){
            throw new \LogicException("ここに来ることはない");
        }

        if(!class_exists($class)){
            throw new Exception\LazyException;
        }

        return $this->validType(
            $container
                ->getResolver()
                ->getClassResolver($this->class)
                ->getInstanceGenerator()
                ->generate($container, $this->parameters, $this->types)
            ,
            $expectedType
        );
    }

    /**
     * パラメータ自動解決用の値を追加する
     *
     * @param   string  $key
     *  ポジションもしくはパラメータ名
     * @param   mixed   $value
     *  値
     *
     * @return  $this
     *
     * @throws  Exception\LockedException
     */
    public function addParameter($key, $value){
        if($this->isLocked()){
            throw new Exception\LockedException();
        }

        if(!is_int($key) && !is_string($key)){
            throw new \InvalidArgumentException;
        }

        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * 型名自動解決用の値を追加する
     *
     * @param   string  $class
     *  クラス名
     * @param   object|LazyInterface    $value
     *  値
     *
     * @return  $this
     *
     * @throws Exception\LockedException
     */
    public function addType(string $class, $value){
        if($this->isLocked()){
            throw new Exception\LockedException();
        }

        if(!class_exists($class)){
            throw new \InvalidArgumentException;
        }

        $this->types[$class]    = $value;

        return $this;
    }
}
