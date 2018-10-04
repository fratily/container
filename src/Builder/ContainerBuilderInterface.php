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
interface ContainerBuilderInterface{

    /**
     * リゾルバを取得する
     *
     * @return  Resolver\Resolver
     */
    public function getResolver();

    /**
     * サービスのリストを取得する
     *
     * @return  object[]|Lazy\LazyInterface[]
     */
    public function getServices();

    /**
     * タグ付けがされたサービスIDのリストを取得する
     *
     * @return  string[][]
     */
    public function getTaggedServicesId();

    /**
     * サービスを登録する
     *
     * @param   string  $id
     *  サービスID
     * @param   string|LazyInterface|object $service
     *  サービス
     * @param   string[]    $tags
     *  サービスにつけるタグの配列
     * @param   string[]    $types
     *  パラメータの型指定による自動解決時に、
     *  このサービスがどのようなクラス指定に使用されるかを示す配列
     *
     * @return  $this
     */
    public function add(
        string $id,
        $service,
        array $tags = [],
        array $types = []
    );

    /**
     * クラスのインスタンス化モードをシングルトンにする
     *
     * @param   string  $class
     *  クラス名
     *
     * @return  $this
     */
    public function isSingleton(string $class);

    /**
     * クラスのインスタンス化モードをプロトタイプにする
     *
     * @param   string  $class
     *  クラス名
     *
     * @return  $this
     */
    public function isPrototype(string $class);

    /**
     *  パラメーターを登録する
     *
     * @param   string  $class
     *  クラス名
     * @param   int|string  $parameter
     *  パラメーター名もしくはパラメーターポジション
     * @param   mixed   $value
     *  インジェクションする値
     *
     * @return  $this
     */
    public function addParameter(string $class, $parameter, $value);

    /**
     * セッターを登録する
     *
     * @param   string  $class
     *  クラス名
     * @param   string  $setter
     *  メソッド名
     * @param type $value
     *  インジェクションする値
     *
     * @return  $this
     */
    public function addSetter(string $class, string $setter, $value);

    /**
     * プロパティを登録する
     *
     * @param   string  $class
     *  クラス名
     * @param   string  $property
     *  プロパティ名
     * @param   mixed   $value
     *  インジェクションする値
     *
     * @return  $this
     */
    public function addProperty(string $class, string $property, $value);

    /**
     * パラメータ登録のオブジェクティブ版を取得する
     *
     * @param   string  $class
     *  クラス名
     *
     * @return  ParameterBuilder
     */
    public function parameter(string $class);

    /**
     * セッター登録のオブジェクティブ版を取得する
     *
     * @param   string  $class
     *  クラス名
     *
     * @return  SetterBuilder
     */
    public function setter(string $class);

    /**
     * プロパティ登録のオブジェクティブ版を取得する
     *
     * @param   string  $class
     *  クラス名
     *
     * @return  PropertyBuilder
     */
    public function property(string $class);
}