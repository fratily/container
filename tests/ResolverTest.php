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
namespace Fratily\Tests\Container;

use Fratily\Container\Container;
use Fratily\Container\Resolver;
use Fratily\Container\Builder\Value\LazyBuilder;
use Fratily\Container\Builder\Value\Lazy\LazyInterface;

/**
 *
 */
class ResolverTest extends \PHPUnit\Framework\TestCase{

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var Container
     */
    private $container;

    public function setUp(){
        $this->container    = $this->createMock(Container::class)
            ->method("has")
            ->will($this->returnValueMap([
                ["stdClass", true],
                ["service.name", true],
            ]))
        ;

        $this->resolver = new Resolver($this->container);
    }

    public function testResolveParameterResolvedByPosition(){
        $expected   = "value";
        $position   = 1;
        $parameter  = $this->createMock(\ReflectionParameter::class)
            ->method("getPosition")
            ->willReturn($position)
        ;

        $result = $this->resolver->resolveParameter(
            $parameter,
            [$position => $expected],
            [],
            []
        );

        $this->assertSame($expected, $result);
    }

    public function testResolveParameterResolvedByName(){
        $expected   = "value";
        $name       = "name";
        $parameter  = $this->createMock(\ReflectionParameter::class)
            ->method("getName")
            ->willReturn($name)
        ;

        $result = $this->resolver->resolveParameter(
            $parameter,
            [],
            [$name => $expected],
            []
        );

        $this->assertSame($expected, $result);
    }
}
