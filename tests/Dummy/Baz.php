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
class Baz extends Bar{

    use BazTrait;

    private $piyo;

    public function __construct(HogeInterface $hoge, PiyoInterface $piyo){
        parent::__construct($hoge);
        $this->piyo = $piyo;
    }

    public function getPiyo(){
        return $this->piyo;
    }
}
