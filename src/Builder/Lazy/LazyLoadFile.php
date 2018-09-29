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

/**
 *
 */
class LazyLoadFile implements LazyInterface{

    /**
     * @var string|object
     */
    private $file;

    /**
     * @var bool
     */
    private $once;

    /**
     * @var bool
     */
    private $require;

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
    public function __construct($file, bool $once = false, bool $require = false){
        if(!is_string($file)
            && !($file instanceof LazyInterface)
            && !($file instanceof \SplFileInfo)
        ){
            throw new \InvalidArgumentException();
        }

        $this->file     = $file;
        $this->once     = $once;
        $this->require  = $require;
    }

    /**
     * {@inheritdoc}
     */
    public function load(\Fratily\Container\Container $container){
        $path   = LazyResolver::resolveLazy($this->file);

        if($path instanceof \SplFileInfo){
            $path   = $path->getPathname();
        }

        if(!is_string($path) || !is_file($path)){
            throw new \LogicException;
        }

        if($this->require){
            if($this->once){
                return require_once $path;
            }

            return require $path;
        }

        if($this->once){
            return include_once $path;
        }

        return include $path;
    }
}
