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
class PropertyBuilder{

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
     * @param   ContainerBuilder    $builder
     *  サービスコンテナビルダー
     * @param   string  $class
     *  クラス名
     */
    public function __construct(ContainerBuilder $builder, string $class){
        $this->builder  = $builder;
        $this->class    = $class;
    }

    /**
     * プロパティを登録する
     *
     * @param   string  $property
     *  プロパティ名
     * @param   mixed   $value
     *  インジェクションする値
     *
     * @return  $this
     */
    public function add(string $property, $value){
        $this->builder->addProperty($this->class, $property, $value);

        return $this;
    }
}