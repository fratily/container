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

use Fratily\Container\{
    Container,
    ContainerFactory
};
use Psr\Container\{
    ContainerExceptionInterface,
    NotFoundExceptionInterface
};

/**
 *
 */
class ContainerTest extends \PHPUnit\Framework\TestCase{

    /**
     * 初歩的な使い方のテスト
     */
    public function testBasic(){
        $container  = (new ContainerFactory())->create();
        $value      = new \SplQueue();

        $container->set("queue", $value);

        $this->assertTrue($container->has("queue"));
        $this->assertFalse($container->has("not_found"));

        $this->assertSame($value, $container->get("queue"));
    }

    public function testNotFound(){
        $this->expectException(NotFoundExceptionInterface::class);

        $container  = (new ContainerFactory())->create();

        $container->get("not_found");
    }

    public function testGetWithDelegate(){
        $container1 = (new ContainerFactory())->create();
        $container2 = (new ContainerFactory())->create();
        $queue      = new \SplQueue();

        $container2->set("queue", $queue);

        $container1->addDelegateContainer($container2);

        $this->assertSame($queue, $container1->get("queue"));
    }

    public function testNotFoundWithDelegate(){
        $this->expectException(NotFoundExceptionInterface::class);

        $di = (new ContainerFactory())->create();

        $di->addDelegateContainer(Container::createInstance());

        $di->get("not_found");
    }
}
