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
     * @var string
     */
    protected $type = "mixed";

    /**
     * 値が遅延取得系インスタンスか確認する
     *
     * @param   mixed   $value
     *  確認対象の値
     *
     * @return  bool
     */
    final protected static function isLazyObject($value){
        return is_object($value) && $value instanceof LazyInterface;
    }

    /**
     * 型が正しいか確認する
     *
     * @param   string  $type
     *  期待する型
     * @param   mixed   $value
     *  検証対象の値
     *
     * @return  bool
     */
    final protected static function isValid(string $type, $value){
        return Type::valid($type, $value);
    }

    /**
     * コンストラクタで型が指定されなかった場合に設定される型
     *
     * @return  string
     */
    protected static function getDefaultType(): string{
        return "mixed";
    }

    /**
     * この遅延取得が返しうる型の一覧を取得する
     *
     * これ以外の型をコンストラクタで指定した場合、例外が発生する。
     *
     * @return  string[]|null
     */
    protected static function getAllowTypes(): ?array{
        return null;
    }

    /**
     * 追加型チェック
     *
     * @param   string  $type
     *  指定された型
     *
     * @return  bool
     */
    protected static function reliefTypeCheck(string $type): bool{
        return true;
    }

    /**
     * {@inheritdoc}
     */
    final public function __construct(string $type = null){
        $type   = $type ?? static::getDefaultType();

        if(
            null !== static::getAllowTypes()
            && !in_array($type, static::getAllowTypes())
        ){
            if(!static::reliefTypeCheck($type)){
                throw new \InvalidArgumentException();
            }
        }

        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    final public function getType(){
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    final public function load(Container $container){
        $this->lock();

        $value  = $this->loadValue($container);

        if(!static::isValid($this->getType(), $value)){
            throw new Exception\ExpectedTypeException();
        }

        return $value;
    }

    /**
     * 値をロードする
     *
     * @param   Container   $container
     *  サービスコンテナ
     *
     * @return  mixed
     *
     * @throws  Exception\SettingIsNotCompletedException
     */
    abstract protected function loadValue(Container $container);
}