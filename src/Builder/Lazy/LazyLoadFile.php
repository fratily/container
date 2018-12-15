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
namespace Fratily\Container\Builder\Lazy;

use Fratily\Container\Container;

/**
 *
 */
class LazyLoadFile extends AbstractLazy{

    /**
     * @var string|\SplFileInfo|LazyInterface
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
        if(
            !is_string($file)
            && !(is_object($file) && $file instanceof LazyInterface)
        ){
            throw new \InvalidArgumentException;
        }

        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function load(Container $container, string $expectedType = null){
        $this->lock();

        $path       = $this->file instanceof LazyInterface
            ? $this->file->load($container, Container::T_STRING)
            : $this->file
        ;
        $require    = $this->require instanceof LazyInterface
            ? $this->require->load($container, Container::T_BOOL)
            : $this->require
        ;
        $once       = $this->once instanceof LazyInterface
            ? $this->once->load($container, Container::T_BOOL)
            : $this->once
        ;

        if(!is_string($path) || !is_bool($require) || !is_bool($once)){
            throw new \LogicException("ここに来ることはない");
        }

        if(!is_file($path)){
            throw new Exception\LazyException;
        }

        return $this->validType(
            $require
                ? ($once ? require_once $path : require $path)
                : ($once ? include_once $path : include $path)
            ,
            $expectedType
        );
    }

    /**
     * 読み込みを必須にするか
     *
     * @param   bool|LazyInterface  $require
     *  必須にするか
     *
     * @return  $this
     *
     * @throws  Exception\LockedException
     */
    public function isRequire($require){
        if($this->isLocked()){
            throw new Exception\LockedException();
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
     * @throws  Exception\LockedException
     */
    public function isOnce($once){
        if($this->isLocked()){
            throw new Exception\LockedException();
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
