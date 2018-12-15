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
use Fratily\Container\Builder\LockableInterface;
use Fratily\Container\Builder\LockableTrait;

/**
 *
 */
abstract class AbstractLazy implements LazyInterface, LockableInterface{

    use LockableTrait;

    /**
     * 値がこの遅延ロードの結果として正しいか確認する
     *
     * @param   mixed   $value
     *  検証対象値
     * @param   string|null $expectedType
     *  期待する型
     *
     * @return  void
     *
     * @throws  \LogicException
     */
    protected function validType($value, string $expectedType = null){
        if(null === $expectedType){
            return $value;
        }

        if(
            !array_key_exists($expectedType, Container::TYPE_VALID)
            && !class_exists($expectedType)
        ){
            throw new \InvalidArgumentException;
        }

        if(array_key_exists($expectedType, Container::TYPE_VALID)){
            $callback   = Container::TYPE_VALID[$expectedType];

            if(!is_callable($callback)){
                throw new \LogicException("このエラーを起こしてはならない");
            }

            if(!$callback($value)){
                throw new Exception\ExpectedTypeException(
                    "Expected {$expectedType}, but the value is " . gettype($value)
                );
            }

            return $value;
        }

        if(!is_object($value)){
            throw new Exception\ExpectedTypeException(
                ""
            );
        }

        if(
            $expectedType !== get_class($value)
            && !is_subclass_of($value, $expectedType)
        ){
            throw new Exception\ExpectedTypeException(
                ""
            );
        }

        return $value;
    }
}