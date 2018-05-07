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
    ContainerFactory,
    Injection\LazyGet
};

/**
 *
 */
class LazyGetTest extends \PHPUnit\Framework\TestCase{

    public function testLoad(){
        $container  = (new ContainerFactory())->create();
        $queue      = new \SplQueue();
        $stack      = new \SplStack();
        $lazy_queue = new LazyGet($container, "queue");
        $lazy_stack = new LazyGet($container, "stack");

        $container->set("queue", $queue);
        $container->set("stack", $stack);

        $this->assertSame($queue, $lazy_queue->load());
        $this->assertSame($stack, $lazy_stack->load());
    }
}
