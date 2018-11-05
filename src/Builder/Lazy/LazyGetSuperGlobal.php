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
class LazyGetSuperGlobal implements LazyInterface{

    const GLOBALS   = 0;
    const SERVER    = 1;
    const GET       = 2;
    const POST      = 3;
    const FILES     = 4;
    const COOKIE    = 5;
    const SESSION   = 6;
    const REQUEST   = 7;
    const ENV       = 8;

    const ALLOW_TYPES   = [
        self::GLOBALS   => true,
        self::SERVER    => true,
        self::GET       => true,
        self::POST      => true,
        self::FILES     => true,
        self::COOKIE    => true,
        self::SESSION   => true,
        self::REQUEST   => true,
        self::ENV       => true,
    ];

    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $checkInput;

    protected static function creteExceptionMessage(string $name){
        return "Not found {$name}.";
    }

    protected static function getGlobal(string $name, bool $checkInput = false){
        if(!array_key_exists($name, $GLOBALS)){
            throw new Exception\NotFoundSuperGlobalException(
                static::creteExceptionMessage("\$GLOBAL[{$name}]")
            );
        }

        return $GLOBALS[$name];
    }

    protected static function getServer(string $name, bool $checkInput = false){
        if($checkInput){
            if(false === filter_input(INPUT_SERVER, $name)){
                throw new Exception\NotFoundSuperGlobalException(
                    static::creteExceptionMessage("\$_SERVER[{$name}]")
                );
            }
        }elseif(!array_key_exists($name, $_SERVER)){
            throw new Exception\NotFoundSuperGlobalException(
                static::creteExceptionMessage("\$_SERVER[{$name}]")
            );
        }

        return $checkInput ? filter_input(INPUT_SERVER, $name) : $_SERVER[$name];
    }

    protected static function getQuery(string $name, bool $checkInput = false){
        if($checkInput){
            if(false === filter_input(INPUT_GET, $name)){
                throw new Exception\NotFoundSuperGlobalException(
                    static::creteExceptionMessage("\$_GET[{$name}]")
                );
            }
        }elseif(!array_key_exists($name, $_GET)){
            throw new Exception\NotFoundSuperGlobalException(
                static::creteExceptionMessage("\$_GET[{$name}]")
            );
        }

        return $checkInput ? filter_input(INPUT_GET, $name) : $_GET[$name];
    }

    protected static function getPost(string $name, bool $checkInput = false){
        if($checkInput){
            if(false === filter_input(INPUT_POST, $name)){
                throw new Exception\NotFoundSuperGlobalException(
                    static::creteExceptionMessage("\$_POST[{$name}]")
                );
            }
        }elseif(!array_key_exists($name, $_POST)){
            throw new Exception\NotFoundSuperGlobalException(
                static::creteExceptionMessage("\$_POST[{$name}]")
            );
        }

        return $checkInput ? filter_input(INPUT_POST, $name) : $_POST[$name];
    }

    protected static function getFile(string $name, bool $checkInput = false){
        if(!array_key_exists($name, $_FILES)){
            throw new Exception\NotFoundSuperGlobalException(
                static::creteExceptionMessage("\$_FILES[{$name}]")
            );
        }

        return $_FILES[$name];
    }

    protected static function getCookie(string $name, bool $checkInput = false){
        if($checkInput){
            if(false === filter_input(INPUT_COOKIE, $name)){
                throw new Exception\NotFoundSuperGlobalException(
                    static::creteExceptionMessage("\$_COOKIE[{$name}]")
                );
            }
        }elseif(!array_key_exists($name, $_COOKIE)){
            throw new Exception\NotFoundSuperGlobalException(
                static::creteExceptionMessage("\$_COOKIE[{$name}]")
            );
        }

        return $checkInput ? filter_input(INPUT_COOKIE, $name) : $_COOKIE[$name];
    }

    protected static function getSession(string $name, bool $checkInput = false){
        if($checkInput){
            if(false === filter_input(INPUT_SESSION, $name)){
                throw new Exception\NotFoundSuperGlobalException(
                    static::creteExceptionMessage("\$_SESSION[{$name}]")
                );
            }
        }elseif(!array_key_exists($name, $_SESSION)){
            throw new Exception\NotFoundSuperGlobalException(
                static::creteExceptionMessage("\$_SESSION[{$name}]")
            );
        }

        return $checkInput ? filter_input(INPUT_SESSION, $name) : $_SESSION[$name];
    }

    protected static function getRequest(string $name, bool $checkInput = false){
        if($checkInput){
            if(false === filter_input(INPUT_REQUEST, $name)){
                throw new Exception\NotFoundSuperGlobalException(
                    static::creteExceptionMessage("\$_REQUEST[{$name}]")
                );
            }
        }elseif(!array_key_exists($name, $_REQUEST)){
            throw new Exception\NotFoundSuperGlobalException(
                static::creteExceptionMessage("\$_REQUEST[{$name}]")
            );
        }

        return $checkInput ? filter_input(INPUT_REQUEST, $name) : $_REQUEST[$name];
    }

    protected static function getEnv(string $name, bool $checkInput = false){
        if($checkInput){
            if(false === filter_input(INPUT_ENV, $name)){
                throw new Exception\NotFoundSuperGlobalException(
                    static::creteExceptionMessage("\$_ENV[{$name}]")
                );
            }
        }elseif(false === getenv($name) && !array_key_exists($name, $_ENV)){
            throw new Exception\NotFoundSuperGlobalException(
                static::creteExceptionMessage("\$_ENV[{$name}]")
            );
        }

        if($checkInput){
            return filter_input(INPUT_ENV, $name);
        }

        if(false !== getenv($name)){
            return getenv($name);
        }

        return $_ENV[$name];
    }

    /**
     * Constructor
     *
     * @param   int $type
     *  取得タイプ
     * @param   string  $name
     *  変数名
     * @param   bool    $checkInput
     *  filter_input等を通じてリクエスト時に入力された値か確認するか
     */
    public function __construct(int $type, string $name, bool $checkInput = false){
        if(!array_key_exists($type, self::ALLOW_TYPES)){
            throw new \InvalidArgumentException();
        }

        $this->type         = $type;
        $this->name         = $name;
        $this->checkInput   = $checkInput;
    }

    /**
     * {@inheritdoc}
     */
    public function load(\Fratily\Container\Container $container){
        switch($this->type){
            case self::GLOBALS:
                return static::getGlobal($this->name, $this->checkInput);

            case self::SERVER:
                return static::getServer($this->name, $this->checkInput);

            case self::GET:
                return static::getQuery($this->name, $this->checkInput);

            case self::POST:
                return static::getPost($this->name, $this->checkInput);

            case self::FILES:
                return static::getFile($this->name, $this->checkInput);

            case self::COOKIE:
                return static::getCookie($this->name, $this->checkInput);

            case self::SESSION:
                return static::getSession($this->name, $this->checkInput);

            case self::REQUEST:
                return static::getRequest($this->name, $this->checkInput);

            case self::ENV:
                return static::getEnv($this->name, $this->checkInput);
        }

        throw new \LogicException;
    }
}