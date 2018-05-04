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

use Fratily\Container\Injection\LazyCallable;

/**
 *
 */
class LazyCallableTest extends \PHPUnit\Framework\TestCase{

    /**
     * 正しいコールバックが与えられた場合は正しい値が返される
     *
     * @param   mixed   $expected
     * @param   callable    $callback
     * @param   mixed[] $params
     *
     * @dataProvider    loadDataProvider
     */
    public function testLoad($expected, $callback, $params){
        $lazy   = new LazyCallable($callback, $params);

        $this->assertSame($expected, $lazy->load());
    }
    public function loadDataProvider(){
        return [
            [
                123,
                function(int $a, int $b){return $a + $b;},
                [100, 23],
            ],
            [
                true,
                "is_string",
                ["string"],
            ],
            [
                false,
                new LazyExpectedValue("is_string"),
                [123],
            ],
            [
                "a,b,c",
                [$this, "implode"],
                [",", ["a", "b", "c"]],
            ],
            [
                "a,b,c",
                [new LazyExpectedValue($this), new LazyExpectedValue("implode")],
                [",", ["a", "b", "c"]],
            ],
        ];
    }

    public function implode(string $glue, array $pieces){
        return implode($glue, $pieces);
    }
}