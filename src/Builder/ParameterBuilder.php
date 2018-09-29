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
class ParameterBuilder{

    /**
     * @var ContainerBuilderInterface
     */
    private $builder;

    /**
     * @var string
     */
    private $class;

    /**
     * Constructor
     *
     * @param   ContainerBuilderInterface   $builder
     *  サービスコンテナビルダー
     * @param   string  $class
     *  クラス名
     */
    public function __construct(ContainerBuilderInterface $builder, string $class){
        $this->builder  = $builder;
        $this->class    = $class;
    }

    /**
     * パラメータを登録する
     *
     * @param   int|string  $parameter
     *  パラメーター名もしくはパラメーターポジション
     * @param   mixed   $value
     *  インジェクションする値
     *
     * @return $this
     */
    public function add(string $parameter, $value){
        $this->builder->addParameter($this->class, $parameter, $value);

        return $this;
    }
}