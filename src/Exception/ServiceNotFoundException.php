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
namespace Fratily\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;

/**
 *
 */
class ServiceNotFoundException extends \LogicException implements NotFoundExceptionInterface{

    const _MSG  = "Service not found in container.";

    const MSG   = "Service '{id}' is not found in container.";

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
     * 存在しなかったサービス名を取得する
     *
     * @return  string|null
     */
    public function getId(){
        return $this->id;
    }

    /**
     * 存在しなかったサービス名を登録する
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