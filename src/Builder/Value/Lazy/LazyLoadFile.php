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
class LazyLoadFile extends AbstractLazy{

    /**
     * @var string|LazyInterface
     */
    private $file;

    /**
     * @var bool|LazyInterface
     */
    private $require    = false;

    /**
     * @var bool|LazyInterface
     */
    private $once       = false;

    /**
     * Constructor
     *
     * @param   string|LazyInterface|\SplFileInfo   $file
     *  読み込むするファイル
     * @param   bool    $once
     *  onceフラグ
     * @param   bool    $require
     *  必須フラグ
     */
    public function __construct($file){
        if(!is_string($file) && !$this->isLazyObject($file)){
            throw new \InvalidArgumentException;
        }

        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadValue(Container $container){
        $path       = $this->isLazyObject($this->file)
            ? $this->file->load($container, "string")
            : $this->file
        ;
        $require    = $this->isLazyObject($this->require)
            ? $this->require->load($container, "bool")
            : $this->require
        ;
        $once       = $this->isLazyObject($this->once)
            ? $this->once->load($container, "bool")
            : $this->once
        ;

        if(!is_file($path)){
            throw new Exception\LazyException;
        }

        return $require
            ? ($once ? require_once $path : require $path)
            : ($once ? include_once $path : include $path)
        ;
    }

    /**
     * 読み込みを必須にするか
     *
     * @param   bool|LazyInterface  $require
     *  必須にするか
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function isRrequire($require){
        if($this->isLocked()){
            throw new LockedException();
        }

        if(
            !is_bool($require)
            && !(is_object($require) && $require instanceof LazyInterface)
        ){
            throw new \InvalidArgumentException;
        }

        $this->require  = $require;

        return $this;
    }

    /**
     * 読み込みを一度に限るか
     *
     * @param   bool|LazyInterface  $once
     *  一度に限るか
     *
     * @return  $this
     *
     * @throws  LockedException
     */
    public function isOnce($once){
        if($this->isLocked()){
            throw new LockedException();
        }

        if(
            !is_bool($once)
            && !(is_object($once) && $once instanceof LazyInterface)
        ){
            throw new \InvalidArgumentException;
        }

        $this->once = $once;

        return $this;
    }
}
