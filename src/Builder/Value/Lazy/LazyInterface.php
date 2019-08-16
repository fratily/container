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
use Fratily\Container\Builder\LockableInterface;

/**
 *
 */
interface LazyInterface extends LockableInterface
{

    /**
     * Constructor
     *
     * @param   string  $type
     *  この遅延取得の値の型
     */
    public function __construct(string $type);

    /**
     * この遅延取得の型を取得する
     *
     * @return  string
     */
    public function getType();

    /**
     * 遅延実行用メソッド
     *
     * @param   Container   $container
     *  サービスコンテナ
     *
     * @return  mixed
     *
     * @throws  Exception\ExpectedTypeException
     * @throws  Exception\SettingIsNotCompletedException
     */
    public function load(Container $container);
}
