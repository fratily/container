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
namespace Fratily\Container\Injection;

/**
 *
 */
class LazyInclude implements LazyInterface{

    /**
     * @var string|object
     */
    protected $file;

    /**
     * Constructor
     *
     * @param   string|LazyInterface|\SplFileInfo   $file
     */
    public function __construct($file){
        if(!is_string($file)
            && !($file instanceof LazyInterface)
            && !($file instanceof \SplFileInfo)
        ){
            throw new \InvalidArgumentException();
        }

        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function load(){
        $this->file = LazyResolver::resolveLazy($this->file);
        $path       = null;

        if(is_string($this->file)){
            $path   = $this->file;
        }else if($this->file instanceof \SplFileInfo){
            $path   = $this->file->getPathname();
        }else{
            throw new \LogicException;
        }

        if(!is_file($path)){
            throw new \LogicException;
        }

        return include(realpath($path));
    }
}
