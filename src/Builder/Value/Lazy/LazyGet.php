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
class LazyGet extends AbstractLazy{

    /**
     * @var string|LazyInterface|null
     */
    private $id;

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
    public function loadValue(Container $container){
        if(null === $this->id){
            throw new Exception\SettingIsNotCompletedException();
        }

        return $container->get(LazyResolver::resolve($container, $this->id));
    }

    /**
     * サービスIDを設定する
     *
     * @param   string|LazyInterface    $id
     *  サービスID
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function id($id){
        if($this->isLocked()){
            throw new LockedException();
        }

        if(
            !is_string($id)
            && !(static::isLazyObject($id) && "string" === $id->getType())
        ){
            throw new \InvalidArgumentException();
        }

        $this->id   = $id;

        return $this;
    }
}
