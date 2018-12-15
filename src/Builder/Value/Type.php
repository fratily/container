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
class Type{

    /**
     * @var callable[]
     */
    private static $validators  = [
        // special
        "mixed"     => self::class . "isMixed",
        // scalar
        "bool"      => "is_bool",
        "int"       => "is_int",
        "float"     => "is_float",
        "string"    => "is_string",
        "scalar"    => "is_scalar",
        "numeric"   => "is_numeric",
        // structure
        "array"     => "is_array",
        "object"    => "is_object",
        "resource"  => "is_resource",
        "callable"  => "is_callable",
        // implements
        "countable" => "is_countable",
        "iterable"  => "is_iterable",
    ];

    /**
     * バリデーションを行う
     *
     * @param   string  $type
     *  型
     * @param   mixed   $value
     *  値
     *
     * @return  bool
     */
    public static function valid(string $type, $value){
        // もし配列指定なら再帰的にチェックを行う
        if("[]" === substr($value, -2)){
            if(!is_array($value)){
                return false;
            }

            $type   = substr($type, 0, -2);

            foreach($value as $_value){
                if(!self::valid($type, $_value)){
                    return false;
                }
            }

            return true;
        }

        if(":" === substr($type, 0, 1)){
            $types  = explode("|", substr($type, 1));

            foreach($types as $type){
                $validator  = self::$validators[$type] ?? null;

                if(null !== $validator && $validator($value)){
                    return true;
                }
            }

            return false;
        }

        if(!class_exists($type)){
            throw new \InvalidArgumentException;
        }

        if(!is_object($value)){
            return false;
        }

        if(
            get_class($value) === ltrim($type, "\\")
            || is_subclass_of($value, $type)
        ){
            return true;
        }

        return false;
    }

    public static function addType(string $type, callable $validator){
        if(array_key_exists($type, self::$validators)){
            throw new \InvalidArgumentException;
        }

        self::$validators[$type]    = $validator;
    }

    public static function isMixed(){
        return true;
    }
}