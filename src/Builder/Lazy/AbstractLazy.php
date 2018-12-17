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

use Fratily\Container\Builder\LockableInterface;
use Fratily\Container\Builder\LockableTrait;
use Fratily\Container\Builder\Type;

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
        if(null !== $expectedType && !Type::valid($expectedType, $value)){
            throw new Exception\ExpectedTypeException();
        }

        return $value;
    }
}