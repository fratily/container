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
namespace Fratily\Tests\Container\Dummy;

/**
 *
 */
class Foo{

    use FooTrait;

    private $hoge;

    private $fuga;

    public function __construct(HogeInterface $hoge){
        $this->hoge = $hoge;
    }

    public function setFuga(FugaInterface $fuga){
        $this->fuga = $fuga;
    }

    public function getHoge(){
        return $this->hoge;
    }

    public function getFuga(){
        return $this->fuga;
    }
}
