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

use Fratily\Container\Injection\LazyArray;

/**
 *
 */
class LazyArrayTest extends \PHPUnit\Framework\TestCase{

    public function testLoad(){
        $lazy   = new LazyArray([
            "string",
            new LazyArray([
                new LazyArray([1,2,3]),
                "string",
            ]),
        ]);

        $this->assertSame([
            "string",
            [
                [1,2,3],
                "string"
            ],
        ], $lazy->load());
    }
}
