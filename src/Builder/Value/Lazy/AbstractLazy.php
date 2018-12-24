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
namespace Fratily\Container\Builder\Value\Lazy;

use Fratily\Container\Container;
use Fratily\Container\Builder\LockableTrait;
use Fratily\Container\Builder\Value\Type;

/**
 *
 */
abstract class AbstractLazy implements LazyInterface{

    use LockableTrait;

    /**
     * 値が遅延取得系インスタンスか確認する
     *
     * @param   mixed   $value
     *  確認対象の値
     *
     * @return  bool
     */
    protected function isLazyObject($value){
        return is_object($value) && $value instanceof LazyInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function load(Container $container, string $expectedType = "mixed"){
        $this->lock();

        if(!Type::valid($expectedType, $value = $this->loadValue($container))){
            throw new Exception\ExpectedTypeException;
        }

        return $value;
    }

    /**
     * 遅延実行用メソッド
     *
     * @param   Container   $container
     *  サービスコンテナ
     *
     * @return  mixed
     *
     * @throws  Exception\LazyException
     */
    abstract protected function loadValue(Container $container);
}