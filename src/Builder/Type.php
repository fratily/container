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

/**
 *
 */
final class Type{

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
        // OR指定ならそれぞれについて調べる
        if(false !== strpos($type, "|")){
            foreach(explode("|", $type) as $_type){
                if(self::valid($_type, $value)){
                    return true;
                }
            }

            return false;
        }

        // もし配列指定なら再帰的にチェックを行う
        if("[]" === substr($type, -2)){
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

        if(array_key_exists($type, self::$validators)){
            $validator  = self::$validators[$type];

            return (bool) $validator($value);
        }

        $type   = ltrim($type, "\\");

        return class_exists($type) && is_object($value) && $value instanceof $type;
    }

    /**
     * 型を追加する
     *
     * 追加した型は先頭に : が付与されることに注意
     *
     * @param   string  $type
     *  型名
     * @param   callable    $validator
     *  バリデーションコールバック
     *
     * @return  void
     */
    public static function addType(string $type, callable $validator){
        $type   = ":" . $type;

        if(array_key_exists($type, self::$validators)){
            throw new \InvalidArgumentException;
        }

        self::$validators[$type]    = $validator;
    }

    /**
     * mixed validator
     *
     * @return  true
     */
    public static function isMixed(){
        return true;
    }
}