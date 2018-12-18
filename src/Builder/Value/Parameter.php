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

/**
 *
 */
class Parameter{

    use TagTrait;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var bool
     */
    private $valueOverwritable  = true;

    /**
     * 値を取得する
     *
     * @return  mixed
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * 値が書き換え可能か確認する
     *
     * @return  bool
     */
    public function isOverWritableValue(){
        return $this->valueOverwritable;
    }

    /**
     * 値を設定する
     *
     * @param   mixed   $value
     *  値
     * @param   bool    $allowOverwrite
     *  書き換えを許可するか
     *
     * @return  $this
     */
    public function setValue($value, bool $allowOverwrite = true){
        if(!$this->isOverWritableValue()){
            throw new \LogicException;
        }

        $this->value                = $value;
        $this->valueOverwritable    = $allowOverwrite;

        return $this;
    }
}