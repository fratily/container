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

use Fratily\Container\Injection\LazyRequire;

/**
 *
 */
class LazyRequireTest extends \PHPUnit\Framework\TestCase{

    /**
     *
     * @param   mixed   $expected
     * @param   mixed   $file
     *
     * @dataProvider    loadDataProvider
     */
    public function testLoad($expected, $file){
        $lazy   = new LazyRequire($file);

        $this->assertSame($expected, $lazy->load());
    }

    public function loadDataProvider(){
        $path   = __DIR__ . DIRECTORY_SEPARATOR . "include.php";
        return [
            [[123], $path],
            [[123], new \SplFileInfo($path)],
            [[123], new LazyExpectedValue($path)],
            [[123], new LazyExpectedValue(new \SplFileInfo($path))],
        ];
    }
}