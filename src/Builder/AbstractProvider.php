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

use Fratily\Container\Container;
use Fratily\Container\Builder\Value\LazyBuilder;

/**
 *
 */
abstract class AbstractProvider
{

    /**
     * @var ContainerBuilder
     */
    private $builder;

    /**
     * Constructor
     *
     * @param   ContainerBuilder    $builder
     *  サービスコンテナビルダー
     */
    final public function __construct(ContainerBuilder $builder)
    {
        $this->builder  = $builder;
    }

    /**
     * サービスを取得する
     *
     * @param   string  $id
     *  サービスID
     *
     * @return  Value\Service
     */
    final public function service(string $id)
    {
        return $this->builder->service($id);
    }

    /**
     * パラメーターを取得する
     *
     * @param   string  $id
     *  パラメーターID
     *
     * @return  Value\Parameter
     */
    final public function parameter(string $id)
    {
        return $this->builder->parameter($id);
    }

    /**
     * DI設定を取得する
     *
     * @param   string  $id
     *  クラス名もしくはサービスID
     *
     * @return  Value\Injection
     */
    final public function injection(string $id)
    {
        return $this->builder->injection($id);
    }

    /**
     * 遅延ビルダーを取得する
     *
     * @return  LazyBuilder
     */
    final public function lazy()
    {
        return $this->builder->getLazyBuilder();
    }

    /**
     * サービスやパラメータなどの定義を行う
     *
     * @param   mixed[] $options
     *  オプションの連想配列
     *
     * @return  void
     */
    public function build(array $options): void
    {
    }

    /**
     * サービスコンテナ生成後にサービスの操作を行う
     *
     * @param   Container   $container
     *  サービスコンテナ
     *
     * @return  void
     */
    public function modify(Container $container): void
    {
    }
}
