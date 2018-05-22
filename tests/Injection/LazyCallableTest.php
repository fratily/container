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
use Fratily\Container\Resolver\Resolver;
use Fratily\Reflection\Reflector\ClassReflector;

/**
 *
 */
class LazyCallableTest extends \PHPUnit\Framework\TestCase{

    /**
     * 正しいコールバックが与えられた場合は正しい値が返される
     */
    public function testLoad(){
        $resolver   = new Resolver(new ClassReflector(), true);
        $queue      = new \SplQueue();
        $params     = [
            "a" => "a",
            2   => "b",
            "c" => "c",
        ];

        $resolver->setType(\SplQueue::class, $queue);

        $lazy   = new LazyCallable($resolver, [$this, "sampleMethod"], $params);

        $lazy->load();

        $this->assertSame("a", $queue->dequeue());
        $this->assertSame("b", $queue->dequeue());
        $this->assertSame("c", $queue->dequeue());
        $this->assertSame(null, $queue->dequeue());
    }

    public function sampleMethod(\SplQueue $queue, $a, $b, $c, $d){
        $queue->enqueue($a);
        $queue->enqueue($b);
        $queue->enqueue($c);
        $queue->enqueue($d);
    }
}