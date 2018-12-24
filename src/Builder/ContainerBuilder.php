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
     * @var Value\Injection
     */
    private $injections = [];

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
    public function service(string $id){
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