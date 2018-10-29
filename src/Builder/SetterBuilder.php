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
class SetterBuilder{

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
     * セッターを登録する
     *
     * @param   string  $setter
     *  メソッド名
     * @param type $value
     *  インジェクションする値
     *
     * @return  $this
     */
    public function add(string $setter, $value){
        $this->builder->addSetter($this->class, $setter, $value);

        return $this;
    }
}