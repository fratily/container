<?php
/**
 * FratilyPHP Container
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento.oka@kentoka.com>
 * @copyright   (c) Kento Oka
 * @license     MIT
 * @since       1.0.0
 */
namespace Fratily\Container\Resolver;

/**
 *
 */
class Reflector{

    /**
     * @var \ReflectionClass[]
     */
    protected $classes = [];

    /**
     * @var \ReflectionParameters[][]
     */
    protected $params = [];

    /**
     * @var string[][]
     */
    protected $traits = [];

    /**
     * クラスのリフレクションを取得する
     *
     * @param   string  $class
     *
     * @return  \ReflectionClass
     */
    public function getClass(string $class){
        if(!class_exists($class)){
            throw new \InvalidArgumentException();
        }

        if(!array_key_exists($class, $this->classes)){
            $this->classes[$class]  = new \ReflectionClass($class);
        }

        return $this->classes[$class];
    }

    /**
     * コンストラクタのパラメータリストを取得する
     *
     * @param   string  $class
     *
     * @return  \ReflectionParameter[]
     */
    public function getParams(string $class){
        if(!class_exists($class)){
            throw new \InvalidArgumentException();
        }

        if(!array_key_exists($class, $this->params)){
            $constructor            = $this->getClass($class)->getConstructor();
            $this->params[$class]   = $constructor === null
                ? [] : $constructor->getParameters()
            ;
        }

        return $this->params[$class];
    }

    /**
     * Traitのリストを取得する
     *
     * @param   string  $class
     *
     * @return  string[]
     */
    public function getTraits(string $class){
        if(!class_exists($class)){
            throw new \InvalidArgumentException();
        }

        if(!array_key_exists($class, $this->traits)){
            $this->traits[$class]   = [];

            do{
                $this->traits[$class]   = array_merge(
                    $this->traits[$class],
                    class_uses($class)
                );
            }while($class = get_parent_class($class));

            $traitsToSearch = $this->traits[$class];

            while(!empty($traitsToSearch)){
                $newTraits              = class_uses(array_pop($traitsToSearch));
                $this->traits[$class]   += $newTraits;
                $traitsToSearch         += $newTraits;
            }

            foreach ($this->traits[$class] as $trait) {
                $this->traits[$class]   += class_uses($trait);
            }

            $this->traits[$class] = array_unique($this->traits[$class]);
        }

        return $this->traits[$class];
    }
}
