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
namespace Fratily\Container\Builder\Traits;

use Fratily\Container\Builder\Lazy\LazyInterface;
use Fratily\Container\Container;
use Fratily\Container\Builder\LockableTrait;
use Fratily\Container\Builder\Exception\LockedException;

trait TagsTrait
{
    /**
     * @var bool[]
     */
    private $tags = [];


    /**
     * エイリアスのリストを取得する
     *
     * @return  string[]
     */
    public function getAliases()
    {
        return array_keys($this->aliases);
    }

    /**
     * エイリアスを追加する
     *
     * @param   string  $alias
     *  エイリアス
     *
     * @return  $this
     */
    public function addAlias(string $alias)
    {
        $alias  = ltrim($alias, "\\");

        if (1 !== preg_match(Container::REGEX_KEY, $alias)
            && !class_exists($alias)
            && !interface_exists($alias)
        ) {
            throw new \InvalidArgumentException();
        }

        $this->aliases[$alias]  = true;

        return $this;
    }

    /**
     * エイリアスを削除する
     *
     * @param   string  $alias
     *  エイリアス
     *
     * @return  $this
     */
    public function removeAlias(string $alias)
    {
        if (array_key_exists($alias, $this->aliases)) {
            unset($this->aliases[$alias]);
        }

        return $this;
    }
}
