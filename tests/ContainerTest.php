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
namespace Fratily\Tests\Container;

use Fratily\Container\Container;

/**
 *
 */
class ContainerTest extends \PHPUnit\Framework\TestCase{

    /**
     * 初歩的な使い方のテスト
     */
    public function testBasic(){
        $di     = Container::createInstance();
        $q_k    = "queue";
        $q_v    = new \SplQueue();

        $di->set($q_k, $q_v);

        $this->assertTrue($di->has($q_k));
        $this->assertFalse($di->has("not_found"));

        $this->assertSame($q_v, $di->get($q_k));
    }
}
