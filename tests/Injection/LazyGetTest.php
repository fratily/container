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
namespace Fratily\Tests\Container\Injection;

use Fratily\Container\{
    Container,
    Injection\LazyGet
};

/**
 *
 */
class LazyGetTest extends \PHPUnit\Framework\TestCase{

    public function testLoad(){
        $di     = Container::createInstance();

        $q_k    = "queue";
        $q_v    = new \SplQueue();
        $s_k    = "stack";
        $s_v    = new \SplStack();

        $di->set($q_k, $q_v);
        $di->set($s_k, $s_v);

        $lazy_q = new LazyGet($di, $q_k);
        $lazy_s = new LazyGet($di, $s_k);

        $this->assertSame($q_v, $lazy_q->load());
        $this->assertSame($s_v, $lazy_s->load());
    }
}
