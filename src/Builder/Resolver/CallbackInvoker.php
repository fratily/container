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
namespace Fratily\Container\Builder\Resolver;

use Fratily\Container\Container;
use Fratily\Container\Builder\Lazy\LazyResolver;
use Fratily\Reflection\ReflectionCallable;

/**
 *
 */
class CallbackInvoker{

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var callable
     */
    private $callback;

    /**
     * Constructor
     *
     * @apram   Resolver    $resolver
     *  リゾルバ
     * @param   callable    $callback
     *  実行するコールバック
     */
    public function __construct(Resolver $resolver, callable $callback){
        $this->resolver = $resolver;
        $this->callback = $callback;
    }

    /**
     * メソッドを実行しその結果を取得する
     *
     * @param   Container   $container
     *  サービスコンテナ
     * @param   mixed[] $parameters
     *  パラメータの連想配列
     * @param   mixed[] $types
     *  型の連想配列
     *
     * @return  mixed
     */
    public function invoke(Container $container, array $parameters = [], array $types = []){
        $reflection = new ReflectionCallable($this->callback);

        return call_user_func_array(
            $this->callback,
            LazyResolver::resolveLazyArray(
                $container,
                $this->resolver->parameterResolve(
                    $reflection->getReflection(),
                    $parameters,
                    $types
                )
            )
        );
    }
}
