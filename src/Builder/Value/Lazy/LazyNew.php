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
use Fratily\Container\Builder\Value\Injection;
use Fratily\Container\Builder\Exception\LockedException;

/**
 *
 */
class LazyNew extends AbstractLazy{

    /**
     * @var string|LazyInterface|null
     */
    private $class;

    /**
     * @var Injection|null
     */
    private $injection;

    /**
     * {@inheritdoc}
     */
    protected static function getDefaultType(): string{
        return "object";
    }

    /**
     * {@inheritdoc}
     */
    protected static function getAllowTypes(): ?array{
        return ["object"];
    }

    /**
     * {@inheritdoc}
     */
    protected static function reliefTypeCheck(string $type): bool{
        return class_exists($type) || interface_exists($type);
    }

    /**
     * {@inheritdoc}
     */
    public function lock(){
        if(null !== $this->injection){
            $this->injection->lock();
        }

        return parent::lock();
    }

    /**
     * {@inheritdoc}
     */
    public function loadValue(Container $container){
        if(null === $this->class){
            throw new Exception\SettingIsNotCompletedException();
        }

        $class  = LazyResolver::resolve($container, $this->class);

        if(!class_exists($class)){
            throw new Exception\SettingIsNotCompletedException();
        }

        return $container->new($class, $this->injection);
    }

    /**
     * クラス名を設定する
     *
     * @param   string|LazyInterface    $class
     *  クラス名
     *
     * @return  $this
     *
     * @throws  LockedException
     * @throws  \ReflectionException
     */
    public function class($class){
        if($this->isLocked()){
            throw new LockedException();
        }

        if(
            is_string($class)
            && !(
                static::isLazyObject($$class)
                && "string" === $class->getType()
            )
        ){
            throw new \InvalidArgumentException();
        }

        if(is_string($class) && !(new \ReflectionClass($class))->isInstantiable()){
            throw new \InvalidArgumentException();
        }

        $this->class    = $class;

        return $this;
    }

    /**
     * 依存性定義インスタンスを取得する
     *
     * @return  Injection
     */
    public function injection(){
        if($this->isLocked()){
            throw new LockedException();
        }

        if(null === $this->injection){
            $this->injection    = new Injection();
        }

        return $this->injection;
    }
}
