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
namespace Fratily\Container\Builder\Value;

use Fratily\Container\Container;
use Fratily\Container\Builder\LockableInterface;
use Fratily\Container\Builder\Exception\LockedException;


/**
 *
 */
trait TagTrait{

    /**
     * @var mixed[]
     */
    private $tags   = [];

    /**
     * タグを取得する
     *
     * @return  string[]
     */
    public function getTags(){
        return array_keys($this->tags);
    }

    /**
     * タグを追加する
     *
     * @param   string  $tag
     *  タグ名
     *
     * @return  $this
     */
    public function addTag(string $tag){
        if($this instanceof LockableInterface && $this->isLocked()){
            throw new LockedException();
        }

        if(1 !== preg_match(Container::REGEX_KEY, $tag)){
            throw new \InvalidArgumentException;
        }

        $this->tags[$tag]   = true;

        return $this;
    }
}