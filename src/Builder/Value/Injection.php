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

use Fratily\Container\Builder\LockableInterface;
use Fratily\Container\Builder\LockableTrait;
use Fratily\Container\Builder\Exception\LockedException;

class Injection implements LockableInterface{

    use LockableTrait;

    const PARAM_POS     = "pos";
    const PARAM_NAME    = "name";

    /**
     * @var mixed[][]
     */
    private $parameters = [
        self::PARAM_POS     => [],
        self::PARAM_NAME    => [],
    ];

    /**
     * @var array[]
     */
    private $setters    = [];

    /**
     * パラメーターを取得する
     *
     * @param   string  $type
     *  パラメータータイプ
     *
     * @return  mixed[]
     */
    public function getParameters(string $type = self::PARAM_POS){
        if(self::PARAM_POS !== $type && self::PARAM_NAME !== $type){
            throw new \InvalidArgumentException;
        }

        return $this->parameters[$type];
    }

    /**
     * パラメーターを設定する
     *
     * @param   int|string  $key
     *  キー
     * @param   mixed   $value
     *  値
     *
     * @return  $this
     */
    public function parameter($key, $value){
        if($this->isLocked()){
            throw new LockedException();
        }

        if(!is_int($key) && !is_string($key)){
            throw new \InvalidArgumentException;
        }

        $this->parameters[is_int($key) ? self::PARAM_POS : self::PARAM_NAME][$key]  = $value;

        return $this;
    }

    /**
     * セッターメソッドをキーとした引数配列のリストを取得する
     *
     * @return  array[]
     */
    public function getSetters(){
        return $this->setters;
    }

    /**
     * セッターを設定する
     *
     * @param   string  $method
     *  メソッド名
     * @param   mixed   ...$args
     *  引数
     *
     * @return  $this
     */
    public function setter(string $method, ...$args){
        if($this->isLocked()){
            throw new LockedException();
        }

        $this->setters[$method] = $args;

        return $this;
    }
}