# FratilyPHP Container

This is simple Dependency Injection Container.

## Install

```bash
$ composer require fratily/container
```

## Usage

```php
use Fratily/Container/Builder/AbstractProvider;
use Fratily/Container/Builder/ContainerBuilder;
use Fratily/Container/ContainerFactory;

class Foo
{
    public function __construct(
        LoggerInterface $logger,
        SplQueue $queue,
        string $prefix
    ) {/* ... */}

    public function setCache(CacheInterface $cache): void {/* ... */}

    public function lock(): void {/* ... */}    
}

class SampleProvider extends AbstractProvider
{
    public function build(ContainerBuilder $builder): void
    {
        $builder->injection(Foo::class)
            ->addArgument("queue", new LazyNew(SplQueue::class))
            ->addArgument(3, new LazyGetParameter("parameter.prefix"))
            ->addSetter("setCache", new LazyGet(CacheInterface::class))
        ;
    
        $builder->parameter("parameter.prefix")
            ->setValue("this.is.prefix")
        ;
        
        $builder->service("service.foo")
            ->setValueByClass(Foo::class)
        ;
    
        $builder->service(LoggerInterface::class)
            ->setValueByFactory(
                function () {
                    return new Logger();
                }
            )
        ;

        $builder->service(CacheInterface::class)
            ->setValueByFactory(
                function () {
                    return new Cache();
                }
            )
        ;
    }

    public function modify(Container $container): void
    {
        $container->get("service.foo")->lock();
    }
}

$factory = new ContainerFactory();

$factory->add(new SampleProvider());

$container = $factory->create();

$foo = $container->get("service.foo");
```