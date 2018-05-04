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

use Fratily\Container\Injection\LazyResolver;

/**
 *
 */
class LazyResolverTest extends \PHPUnit\Framework\TestCase{

    /**
     *
     * @param   mixed   $expected
     * @param   mixed   $val
     *
     * @dataProvider    resolveLazyDataProvider
     */
    public function testResolveLazy($expected, $val){
        $this->assertSame($expected, LazyResolver::resolveLazy($val));
    }

    /**
     *
     * @param   mixed   $expected
     * @param   mixed[] $val
     *
     * @dataProvider    resolveLazyArrayDataProvider
     */
    public function testResolveLazyArray($expected, $val){
        $this->assertSame($expected, LazyResolver::resolveLazyArray($val));
    }

    public function resolveLazyDataProvider(){
        return [
            ["string", "string"],
            ["string", new LazyExpectedValue("string")],
        ];
    }

    public function resolveLazyArrayDataProvider(){
        return [
            [
                ["string", 123],
                ["string", 123],
            ],
            [
                ["string", 123],
                ["string", new LazyExpectedValue(123)],
            ],
        ];
    }
}