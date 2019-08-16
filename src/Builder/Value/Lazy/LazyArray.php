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
use Fratily\Container\Builder\Exception\LockedException;

/**
 *
 */
class LazyArray extends AbstractLazy
{

    /**
     * @var array
     */
    private $value;

    /**
     * {@inheritdoc}
     */
    protected static function getDefaultType(): string
    {
        return "array";
    }

    /**
     * {@inheritdoc}
     */
    protected static function getAllowTypes(): ?array
    {
        return ["array"];
    }

    /**
     * {@inheritdoc}
     */
    public function loadValue(Container $container)
    {
        if (null === $this->value) {
            throw new Exception\SettingIsNotCompletedException();
        }

        return LazyResolver::resolveArray($container, $this->value);
    }

    /**
     * 配列もしくは連想配列を設定する
     *
     * @param   array $value
     *  遅延解決する配列もしくは連想配列
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function value(array $value)
    {
        if ($this->isLocked()) {
            throw new LockedException();
        }

        $this->value    = $value;

        return $this;
    }

    /**
     * 配列を設定する
     *
     * 可変長引数を取るので、配列を設定したくて[]を書くのが面倒な時に使う。
     * 基本的に$this->value()を使うように。
     *
     * このメソッドはarrayという名前を使っているため、今後のPHPのアップデートによっては
     * 使用できなくなる可能性がある。
     *
     * @param   mixed   ...$vars
     *  遅延解決する値のリスト
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function array(...$vars)
    {
        return $this->value($vars);
    }
}
