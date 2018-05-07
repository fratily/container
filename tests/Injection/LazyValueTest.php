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
    Injection\LazyValue,
    Resolver\Resolver
};
use Fratiry\Reflection\Reflector\ClassReflector;

/**
 *
 */
class LazyValueTest extends \PHPUnit\Framework\TestCase{

    public function testLoad(){
        $resolver   = new Resolver(new ClassReflector());

        $v1_k   = "v1";
        $v1_v   = "value1";
        $v2_k   = "v2";
        $v2_v   = "value2";

        $resolver->setValue($v1_k, $v1_v);
        $resolver->setValue($v2_k, new LazyExpectedValue($v2_v));

        $v1 = new LazyValue($resolver, $v1_k);
        $v2 = new LazyValue($resolver, $v2_k);
        $v3 = new LazyValue($resolver, "undefine");

        $this->assertSame($v1_v, $v1->load());
        $this->assertSame($v2_v, $v2->load());
        $this->assertSame(null, $v3->load());
    }
}