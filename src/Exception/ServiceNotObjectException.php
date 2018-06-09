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
namespace Fratily\Container\Exception;

use Psr\Container\ContainerExceptionInterface;

/**
 *
 */
class ServiceNotObjectException extends \LogicException implements ContainerExceptionInterface{

    const _MSG  = "The service must be an object.";

    const MSG   = "Service '{id}' is not an object.";

    private $id;

    /**
     * Constructor
     *
     * @param   string  $message    [optional]
     * @param   int $code   [optional]
     * @param   \Throwable  $previous   [optional]
     */
    public function __construct(
        string $message = null,
        int $code = 0,
        \Throwable $previous = null
    ){
        parent::__construct($message ?? self::_MSG, $code, $previous);
    }

    /**
     * オブジェクトでなかったサービスIDを取得する
     *
     * @return  string|null
     */
    public function getId(){
        return $this->id;
    }

    /**
     * オブジェクトではなかったサービス名を登録する
     *
     * 登録したサービス名は変更できない。
     *
     * @param   string  $id
     *
     * @return  void
     */
    public function setId(string $id){
        if($this->id === null){
            $this->id       = $id;
            $this->message  = str_replace("{id}", $this->id, self::MSG);
        }
    }
}