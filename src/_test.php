<?php

require "../vendor/autoload.php";

interface HogeInterface{}
interface FugaInterface{}
interface PiyoInterface{}
interface HogeraInterface{}
class Hoge implements HogeInterface{}
class Fuga implements FugaInterface{}
class Piyo implements PiyoInterface{}
class Hogera implements HogeraInterface{}

interface FooInterface{
    public function setHoge(HogeInterface $hoge);
}
interface BarInterface{
    public function setPiyo(PiyoInterface $piyo);
}

trait FooTrait{

    public $name;
    public $pos;
    public $fuga;

    public function __construct($name, $pos, FugaInterface $fuga){
        $this->name = $name;
        $this->pos  = $pos;
        $this->fuga = $fuga;
    }
}

class Foo implements FooInterface{
    public $hoge;
    use FooTrait;
    public function bar(){}
    public function setHoge(HogeInterface $hoge){
        $this->hoge = $hoge;
    }
}

class Bar extends Foo implements BarInterface{
    public $hogera;
    public $piyo;

    public function __construct(HogeraInterface $hogera, $name, $pos, FugaInterface $fuga){
        parent::__construct($name, $pos, $fuga);
        $this->hogera   = $hogera;
    }
    public function three(){}
    public function two(){}
    public function one(){}
    public function setPiyo(PiyoInterface $piyo){
        $this->piyo = $piyo;
    }
}

class TestContainer extends Fratily\Container\Builder\AbstractContainer{
    public static function build(Fratily\Container\Builder\ContainerBuilderInterface $builder, array $options){
        $builder
            ->add("fuga", Fuga::class, [], [FugaInterface::class])
            ->add("hogera", Hogera::class, [], [HogeraInterface::class])
            ->add("foo", Foo::class)
            ->add("bar", Bar::class)
        ;

        $builder->parameter(FooTrait::class)
            ->add("name", "value_name")
        ;

        $builder->parameter(Foo::class)
            ->add(1, "foo_pos_1")
        ;

        $builder->parameter(Bar::class)
            ->add(2, "bar_pos_2")
        ;

        $builder->setter(FooInterface::class)
            ->add("setHoge", $builder->lazy(function($hoge){return $hoge;}, ["hoge" => $builder->lazyNew(Hoge::class)]))
        ;

        $builder->setter(BarInterface::class)
            ->add("setPiyo", $builder->lazyCallable(function(){return new Piyo;}))
        ;
    }
}

$factory    = new Fratily\Container\ContainerFactory();
$factory->append(TestContainer::class);

$container  = $factory->create();

var_dump(
    $container->get("foo"),
    $container->get("bar")
);



