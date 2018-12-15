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
class ContainerBuilder{

    use LazyBuilderTrait;

    /**
     * @var Resolver\Resolver
     */
    private $resolver;

    /**
     * @var Value\Service[]
     */
    private $services   = [];

    /**
     *
     * @var Value\Parameter[]
     */
    private $parameters = [];

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
    public function getService(string $id){
        if(array_key_exists($id, $this->services)){
            return $this->services[$id];
        }

        $this->addService($id, new Value\Service());

        return $this->services[$id];
    }

    /**
     * サービスを登録する
     *
     * @param   string  $id
     *  サービスID
     * @param   Value\Service   $service
     *  サービス
     *
     * @return  $this
     */
    public function addService(string $id, Value\Service $service){
        if(array_key_exists($id, $this->services)){
            throw new \LogicException;
        }

        if(1 !== preg_match(Container::REGEX_KEY, $id)){
            throw new \InvalidArgumentException;
        }

        $this->services[$id]    = $service;

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
    public function getParameter(string $id){
        if(array_key_exists($id, $this->parameters)){
            return $this->parameters[$id];
        }

        $this->addParameter($id, new Value\Parameter());

        return $this->parameters[$id];
    }

    /**
     * パラメーターを登録する
     *
     * @param   string  $id
     *  パラメーターID
     * @param   Value\Parameter $parameter
     *  パラメーター
     *
     * @return  $this
     */
    public function addParameter(string $id, Value\Parameter $parameter){
        if(array_key_exists($id, $this->parameters)){
            throw new \LogicException;
        }

        if(1 !== preg_match(Container::REGEX_KEY, $id)){
            throw new \InvalidArgumentException;
        }

        $this->parameters[$id]  = $parameter;

        return $this;
    }

    /**
     * サービスもしくはパラメーターを登録する
     *
     * @param   string  $id
     *  サービスもしくはパラメータのID
     * @param   Value\Service|Value\Parameter   $value
     *  サービスもしくはパラメーター
     *
     * @return  $this
     */
    public function add($value){
        if(
            !is_object($value)
            || (
                !$value instanceof Value\Service
                && !$value instanceof Value\Parameter
            )
        ){
            throw new \InvalidArgumentException;
        }

        if($value instanceof Value\Service){
            $this->addService($id, $value);
        }elseif($value instanceof  Value\Parameter){
            $this->addParameter($id, $value);
        }

        return $this;
    }
}