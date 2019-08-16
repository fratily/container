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
namespace Fratily\Container;

use Fratily\Container\Builder\LazyBuilder;
use Fratily\Container\Builder\Lazy\LazyInterface;

/**
 *
 */
class Resolver
{

    /**
     * @var Container
     */
    private $container;

    /**
     * Constructor
     *
     * @param   Container   $container
     *  サービスコンテナ
     */
    public function __construct(Container $container)
    {
        $this->container    = $container;
    }

    /**
     * サービスコンテナを取得する
     *
     * @return  Container
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * クラスがインスタンス化可能か確認する
     *
     * @param   string $class
     *  クラス名
     *
     * @return  bool
     */
    public function isInstantiable(string $class)
    {
        return class_exists($class) && (new \ReflectionClass($class))->isInstantiable();
    }

    /**
     * 関数のパラメータを解決しリストを取得する
     *
     * @param   \ReflectionFunctionAbstract $function
     *  対象関数のリフレクション
     * @param   mixed[] $positions
     *  ポジション指定パラメータ値リスト
     * @param   mixed[] $names
     *  名前指定パラメータ値リスト
     * @param   mixed[] $types
     *  クラス型指定パラメータ値リスト
     *
     * @return  mixed[]
     */
    public function resolveFunctionParameters(
        \ReflectionFunctionAbstract $function,
        array $positions,
        array $names,
        array $types
    ) {
        $result = [];

        foreach ($function->getParameters() as $parameter) {
            try {
                $value  = $this->resolveParameter($parameter, $positions, $names, $types);
            } catch (\Exception $e) {
                throw new Exception\ParameterUnresolvedException(
                    "The value of {$this->getParameterInfoText($parameter)}"
                        . " could not be resolved.",
                    0,
                    $e
                );
            }

            if ($parameter->hasType()) {
                // check value type

                if (null === $value && !$parameter->allowsNull()) {
                    throw new Exception\ParameterUnresolvedException();
                }
            }

            $result[]   = $value;
        }

        return $result;
    }

    /**
     * パラメータの値を解決する
     *
     * @param   \ReflectionParameter    $parameter
     *  パラメータのリフレクション
     * @param   mixed[] $positions
     *  ポジション指定パラメータ値リスト
     * @param   mixed[] $names
     *  名前指定パラメータ値リスト
     * @param   mixed[] $types
     *  クラス型指定パラメータ値リスト
     *
     * @return  mixed
     *
     * @throws  \LogicException
     */
    public function resolveParameter(
        \ReflectionParameter $parameter,
        array $positions,
        array $names,
        array $types
    ) {
        $class  = null;

        if (array_key_exists($parameter->getPosition(), $positions)) {
            return $positions[$parameter->getPosition()];
        }

        if (array_key_exists($parameter->getName(), $names)) {
            return $names[$parameter->getName()];
        }

        if ($parameter->hasType() && !$parameter->getType()->isBuiltin()) {
            try {
                $class  = $parameter->getClass();
            } catch (\ReflectionException $e) {
                throw new Exception\InvalidParameterDefinedException(
                    "Type specification of {$this->getParameterInfoText($parameter)}"
                        . " is invalid.",
                    0,
                    $e
                );
            }

            if (array_key_exists($class->getName(), $types)) {
                return $types[$class->getName()];
            }

            if ($this->getContainer()->has($class->getName())) {
                return LazyBuilder::lazyGet($class->getName());
            }
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if (!$parameter->allowsNull() && null !== $class && $class->isInstantiable()) {
            return LazyBuilder::lazyNew($class->getName());
        }

        return null;
    }

    /**
     * パラメータの位置情報をテキストで取得する
     *
     * @param   \ReflectionParameter    $parameter
     *  パラメーターリフレクション
     *
     * @return  string
     */
    public function getParameterInfoText(\ReflectionParameter $parameter)
    {
        $func   = $parameter->getDeclaringFunction();
        $posStr = str_repeat("\$.., ", $parameter->getPosition());
        $target = $func instanceof \ReflectionMethod
            ? ($func->getDeclaringClass()->getName() . "::" . $func->getName())
            : $func->getName()
        ;

        return $target . "(" . $posStr . "\$" . $parameter->getName() . ")";
    }
}
