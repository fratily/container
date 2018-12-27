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

use Fratily\Container\Container;

/**
 *
 */
class ContainerBuilder implements LockableInterface{

    use LockableTrait;

    /**
     * @var Value\Service[]
     */
    private $services   = [];

    /**
     * @var string[]
     */
    private $aliases    = [];

    /**
     *
     * @var Value\Parameter[]
     */
    private $parameters = [];

    /**
     * @var Value\Injection
     */
    private $injections = [];

    /**
     * {@inheritdoc}
     */
    public function lock(){
        foreach($this->services as $service){
            $service->lock();
        }

        foreach($this->parameters as $parameter){
            $parameter->lock();
        }

        foreach($this->injections as $injection){
            $injection->lock();
        }

        $this->locked   = true;
    }

    /**
     * サービスのリストを取得する
     *
     * @return  Value\Service[]
     */
    public function getServices(){
        return $this->services;
    }

    /**
     * サービスを取得する
     *
     * @param   string  $id
     *  サービスID
     *
     * @return  Value\Service
     */
    public function service(string $id){
        if($this->isLocked()){
            throw new Exception\LockedException;
        }

        $id = ltrim($id, "\\");

        if(!array_key_exists($id, $this->services)){
            if(
                1 !== preg_match(Container::REGEX_KEY, $id)
                && !class_exists($id)
                && !interface_exists($id)
            ){
                throw new \InvalidArgumentException;
            }

            if(array_key_exists($id, $this->parameters)){
                throw new \InvalidArgumentException;
            }

            $this->services[$id]    = new Value\Service();

            if(class_exists($id) || interface_exists($id)){
                $this->services[$id]->setType($id, false);
            }
        }

        return $this->services[$id];
    }

    /**
     * エイリアスをキーとした基サービスIDの連想配列を取得する
     *
     * @return  string[]
     */
    public function getAliases(){
        return $this->aliases;
    }

    /**
     * サービスの別名を追加する
     *
     * @param   string  $alias
     *  エイリアス
     * @param   string  $service
     *  基サービスID
     *
     * @return  $this
     */
    public function addAlias(string $alias, $service){
        if(
            !array_key_exists($alias, $this->aliases)
            && 1 !== preg_match(Container::REGEX_KEY, $alias)
            && !class_exists($id)
            && !interface_exists($id)
        ){
            throw new \InvalidArgumentException;
        }

        $this->aliases[$alias]  = $service;

        return $this;
    }

    /**
     * サービスのエイリアスを削除する
     *
     * @param   string  $alias
     *  エイリアス
     *
     * @return  $this
     */
    public function removeAlias(string $alias){
        if(array_key_exists($alias, $this->aliases)){
            unset($this->aliases[$alias]);
        }

        return $this;
    }

    /**
     * パラメーターのリストを取得する
     *
     * @return  Value\Parameter[]
     */
    public function getParameters(){
        return $this->parameters;
    }

    /**
     * パラメーターを取得する
     *
     * @param   string  $id
     *  パラメーターID
     *
     * @return  Value\Parameter
     */
    public function parameter(string $id){
        if($this->isLocked()){
            throw new Exception\LockedException;
        }

        if(!array_key_exists($id, $this->parameters)){
            if(1 !== preg_match(Container::REGEX_KEY, $id)){
                throw new \InvalidArgumentException;
            }

            if(array_key_exists($id, $this->services)){
                throw new \InvalidArgumentException;
            }

            $this->parameters[$id]  = new Value\Parameter();
        }

        return $this->parameters[$id];
    }

    /**
     * DI設定のリストを取得する
     *
     * @return  Value\Injection[]
     */
    public function getInjections(){
        return $this->injections;
    }

    /**
     * DI設定を取得する
     *
     * @param   string  $id
     *  クラス名もしくはサービスID
     *
     * @return  Value\Injection
     */
    public function injection(string $id){
        if($this->isLocked()){
            throw new Exception\LockedException;
        }

        $id = ltrim($id, "\\");

        if(!array_key_exists($id, $this->injections)){
            if(
                1 !== preg_match(Container::REGEX_KEY, $id)
                && !class_exists($id)
                && !interface_exists($id)
            ){
                throw new \InvalidArgumentException;
            }

            $this->injections[$id]   = new Value\Injection();
        }

        return $this->injections[$id];
    }
}