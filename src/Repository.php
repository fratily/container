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
namespace Fratily\Container;

use Fratily\Container\Builder\Value\Service;
use Fratily\Container\Builder\Value\Parameter;
use Fratily\Container\Builder\Value\Injection;

/**
 *
 */
class Repository
{

    /**
     * @var Service[]
     */
    private $services;

    /**
     * @var Parameter[]
     */
    private $parameters;

    /**
     * @var Injection[]
     */
    private $injections;

    /**
     * @var string[]
     */
    private $aliasedServices    = [];

    /**
     * @var string[]
     */
    private $aliasedParameters  = [];

    /**
     * @var string[][]
     */
    private $taggedServiceLists     = [];

    /**
     * @var string[][]
     */
    private $taggedParameterLists   = [];

    /**
     * Constructor
     *
     * @param   Service[]   $services
     *  サービスIDをキーとした連想配列
     * @param   Parameter[] $parameters
     *  パラメータIDをキーとした連想配列
     * @param   Injection[] $injections
     *  DI設定IDをキーとした連想配列
     */
    public function __construct(array $services, array $parameters, array $injections)
    {
        $this->services     = $services;
        $this->parameters   = $parameters;
        $this->injections   = $injections;

        // せっかくAbstractValueで抽象化しているんだから処理をまとめたい
        foreach ($this->services as $id => $service) {
            foreach ($service->getTags() as $tag) {
                if (!array_key_exists($tag, $this->taggedServiceLists)) {
                    $this->taggedServiceLists[$tag] = [];
                }

                $this->taggedServiceLists[$tag][]   = $id;
            }

            foreach ($service->getAliases() as $alias) {
                if (array_key_exists($alias, $this->aliasedServices)) {
                    throw new \LogicException();
                }

                $this->aliasedServices[$alias]  = $id;
            }
        }

        foreach ($this->parameters as $id => $parameter) {
            foreach ($parameter->getTags() as $tag) {
                if (!array_key_exists($tag, $this->taggedParameterLists)) {
                    $this->taggedParameterLists[$tag]  = [];
                }

                $this->taggedParameterLists[$tag][]    = $id;
            }

            foreach ($parameter->getAliases() as $alias) {
                if (array_key_exists($alias, $this->aliasedParameters)) {
                    throw new \LogicException();
                }

                $this->aliasedParameters[$alias]    = $id;
            }
        }
    }

    /**
     * サービスの一覧を取得する
     *
     * @return  Service[]
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * タグ付けされたサービスのIDリストを取得する
     *
     * @param   string  $tag
     *  タグ
     *
     * @return  string[]
     */
    public function getServiceIdsWithTagged(string $tag)
    {
        return array_key_exists($tag, $this->taggedServiceLists)
            ? $this->taggedServiceLists[$tag]
            : []
        ;
    }

    /**
     * サービスを取得する
     *
     * @param   string  $id
     *  サービスID
     *
     * @return  Service
     *
     * @throws  \LogicException
     */
    public function getService(string $id)
    {
        $id = ltrim($id, "\\");

        if (array_key_exists($id, $this->aliasedServices)) {
            $id = $this->aliasedParameters[$id];
        }

        if (!array_key_exists($id, $this->services)) {
            throw new \LogicException();
        }

        return $this->services[$id];
    }

    /**
     * サービスが存在するか確認する
     *
     * @param   string  $id
     *  サービスID
     *
     * @return  bool
     */
    public function hasService(string $id)
    {
        $id = ltrim($id, "\\");

        return
            array_key_exists($id, $this->services)
            || array_key_exists($id, $this->aliasedServices)
        ;
    }

    /**
     * パラメーターのリストを取得する
     *
     * @return  Parameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * タグ付けされたパラメーターのIDリストを取得する
     *
     * @param   string  $tag
     *  タグ
     *
     * @return  string[]
     */
    public function getParameterIdsWithTagged(string $tag)
    {
        return array_key_exists($tag, $this->taggedParameterLists)
            ? $this->taggedParameterLists[$tag]
            : []
        ;
    }

    /**
     * パラメーターを取得する
     *
     * @param   string  $id
     *  パラメーターID
     *
     * @return  Parameter
     *
     * @throws  \LogicException
     */
    public function getParameter(string $id)
    {
        if (array_key_exists($id, $this->aliasedParameters)) {
            $id = $this->aliasedParameters[$id];
        }

        if (!array_key_exists($id, $this->parameters)) {
            throw new \LogicException();
        }

        return $this->parameters[$id];
    }

    /**
     * パラメーターが存在するか確認する
     *
     * @param   string  $id
     *  パラメーターID
     *
     * @return  bool
     */
    public function hasParameter(string $id)
    {
        return
            array_key_exists($id, $this->parameters)
            || array_key_exists($id, $this->aliasedParameters)
        ;
    }

    /**
     * DI設定のリストを取得する
     *
     * @return  Injection[]
     */
    public function getInjections()
    {
        return $this->injections;
    }

    /**
     * 指定クラスに関連するDI設定のリストを取得する
     *
     * @param   string  $class
     *  クラス名
     *
     * @return  Injection[]
     */
    public function getInjectionsFromClass(string $class)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException();
        }

        $injections = [];

        do {
            if ($this->hasInjection($class)) {
                $injections[]   = $this->getInjection($class);
            }
        } while (false !== ($class = get_parent_class($class)));

        foreach (class_implements($class) as $interface) {
            if ($this->hasInjection($interface)) {
                $injections[]   = $this->getInjection($interface);
            }
        }

        return $injections;
    }

    /**
     * DI設定を取得する
     *
     * @param   string  $id
     *  DI設定ID
     *
     * @return  Injection
     *
     * @throws  \LogicException
     */
    public function getInjection(string $id)
    {
        $id = ltrim($id, "\\");

        if (!array_key_exists($id, $this->injections)) {
            throw new \LogicException();
        }

        return $this->injections[$id];
    }

    /**
     * DI設定が存在するか確認する
     *
     * @param   string  $id
     *  DI設定ID
     *
     * @return  bool
     */
    public function hasInjection(string $id)
    {
        return array_key_exists($id, $this->injections);
    }
}
