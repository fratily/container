<?php
/**
 * FratilyPHP Container
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento.oka@kentoka.com>
 * @copyright   (c) Kento Oka
 * @license     MIT
 * @since       1.0.0
 */
namespace Fratily\Container\Resolver;

/**
 *
 */
class ResolveClass{

    /**
     * @var \ReflectionClass
     */
    private $reflection;

    /**
     * @var mixed[]
     */
    private $params;

    /**
     * @var mixed[]
     */
    private $setters;

    /**
     * Constructor
     *
     * @param   \ReflectionClass    $reflection
     * @param   mixed[] $params
     * @param   mixed[] $setters
     */
    public function __construct(
        \ReflectionClass $reflection,
        array $params,
        array $setters
    ){
        $this->reflection   = $reflection;
        $this->params       = $params;
        $this->setters      = $setters;
    }

    /**
     * インスタンスを生成する
     *
     * @return  object
     */
    public function create(){
        $instance   = $this->reflection->newInstanceArgs($this->params);

        foreach($this->setters as $method => $value){
            $instance->$method($value);
        }

        return $instance;
    }
}
