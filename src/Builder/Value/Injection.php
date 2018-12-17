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
namespace Fratily\Container\Builder\Value;

/**
 *
 */
class Injection{

    const POS   = "pos";
    const NAME  = "name";

    /**
     * @var mixed[]
     */
    private $setters    = [];

    /**
     * @var mixed[][]
     */
    private $parameters = [
        self::POS   => [],
        self::NAME  => [],
    ];

    public function getParameters(string $type){
        if(!array_key_exists($type, $this->parameters)){
            throw new \InvalidArgumentException;
        }

        return $this->parameters[$type];
    }

    public function parameter($parameter, $value){
        if(is_int($parameter)){
            $this->parameters[self::POS][$parameter]    = $value;
        }elseif(is_string($parameter)){
            $this->parameters[self::NAME][$parameter]   = $value;
        }else{
            throw new \InvalidArgumentException;
        }

        return $this;
    }

    public function getSetters(){
        return $this->setters;
    }

    public function setter(string $method, $value){
        $this->setters[$method] = $value;

        return $this;
    }

}