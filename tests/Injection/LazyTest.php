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

use Fratily\Container\Injection\Lazy;

/**
 *
 */
class LazyTest extends \PHPUnit\Framework\TestCase{

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
        $lazy   = new Lazy($callback, $params);

        $this->assertSame($expected, $lazy->load());
    }

    /**
     * newするときに不正な値が渡された場合はInvalidArgumentExceptionがスローされる
     *
     * @param   callable    $callback
     * @param   mixed[] $params
     *
     * @dataProvider    invalidArgumentDataProvider
     */
    public function testInvalidArgument($callback, $params){
        $this->expectException(\InvalidArgumentException::class);

        new Lazy($callback, $params);
    }

    /**
     * 遅延ロードだから通過したコールバックが実はコールバックではなかった場合は\LogicExceptionがスローされる
     *
     * @param   callable    $callback
     * @param   mixed[] $params
     *
     * @dataProvider    notCallableAfterLazyLoadDataProvider
     */
    public function testNotCallableAfterLazyLoad($callback, $params){
        $this->expectException(\LogicException::class);

        $lazy   = new Lazy($callback, $params);

        $lazy->load();
    }

    public function loadDataProvider(){
        return [
            [
                123,
                function(int $a, int $b){return $a + $b;},
                [new LazyExpectedValue(100), 23],
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

    public function invalidArgumentDataProvider(){
        return [
            [
                "undefineFunctionQWERTY",
                [],
            ],
            [
                ["undefineClassQWERTY", "undefineMethodQWERTY"],
                [],
            ],
            [
                [new \SplQueue(), "undefineMethodQWERTY"],
                [],
            ],
        ];
    }

    public function notCallableAfterLazyLoadDataProvider(){
        return [
            [
                new LazyExpectedValue("undefineFunctionQWERTY"),
                []
            ],
            [
                [new LazyExpectedValue("unDefineClassQWERTY"), "undefineMethodQWERTY"],
                []
            ],
            [
                [new \SplQueue(), new LazyExpectedValue("undefineMethodQWERTY")],
                []
            ],
        ];
    }

    public function implode(string $glue, array $pieces){
        return implode($glue, $pieces);
    }
}