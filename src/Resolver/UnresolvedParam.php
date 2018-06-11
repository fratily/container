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
namespace Fratily\Container\Resolver;

/**
 *
 */
class UnresolvedParam{

    /**
     * @var string
     */
    private $name;

    /**
     * Constructor
     *
     * @param   string  $name
     */
    public function __construct(string $name){
        $this->name = $name;
    }

    /**
     * パラメータ名を取得する
     *
     * @return  string
     */
    public function getName(){
        return $this->name;
    }
}
