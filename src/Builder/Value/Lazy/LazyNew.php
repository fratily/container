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
namespace Fratily\Container\Builder\Value\Lazy;

use Fratily\Container\Container;
use Fratily\Container\Builder\Exception\LockedException;

/**
 *
 */
class LazyNew extends AbstractLazy{

    /**
     * @var string|LazyInterface
     */
    private $class;

    /**
     * @var mixed[]
     */
    private $parameters = [];

    /**
     * @var mixed[]
     */
    private $types      = [];

    /**
     * Constructor
     *
     * @param   string|LazyInterface  $class
     *  クラス名
     */
    public function __construct($class){
        if(!(is_string($class) && class_exists($class)) && !$this->isLazyObject($class)){
            throw new \InvalidArgumentException;
        }

        $this->class    = $class;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadValue(Container $container){
        $class  = $this->isLazyObject($this->class)
            ? $this->class->load($container, "string")
            : $this->class
        ;

        if(!class_exists($class)){
            throw new LazyException;
        }

        return $container
            ->getResolver()
            ->getClassResolver($class)
            ->getInstanceGenerator()
            ->generate($container, $this->parameters, $this->types)
        ;
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
    public function parameter($key, $value){
        if($this->isLocked()){
            throw new LockedException();
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
    public function type(string $class, $value){
        if($this->isLocked()){
            throw new LockedException();
        }

        if(!class_exists($class)){
            throw new \InvalidArgumentException;
        }

        $this->types[$class]    = $value;

        return $this;
    }
}
