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

use Fratily\Container\Builder\LockableInterface;
use Fratily\Container\Builder\Exception\LockedException;

/**
 *
 */
interface ValueInterface extends LockableInterface{

    /**
     * 値を取得する
     *
     * @return  mixed
     */
    public function get();

    /**
     * 値を設定する
     *
     * @param   mixed   $value
     *  値
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function set($value);

    /**
     * 型を取得する
     *
     * @return  string
     */
    public function getType();

    /**
     * 型を設定する
     *
     * @param   string  $type
     *  型
     * @param   bool    $overwritable
     *  上書き可能か
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function setType(string $type, bool $overwritable = false);

    /**
     * タグを取得する
     *
     * @return  string[]
     */
    public function getTags();

    /**
     * タグを追加する
     *
     * @param   string  $tag
     *  タグ名
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function addTag(string $tag);
}