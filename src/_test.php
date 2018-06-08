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


$container  = (new Fratily\Container\ContainerFactory())->create(true);
$container->value("name", "name");

$container->type(FugaInterface::class, $container->lazyNew(Fuga::class));
$container->type(HogeraInterface::class, $container->lazyNew(Hogera::class));

$container->param(FooTrait::class, "name", $container->lazyValue("name"));
$container->param(Foo::class, 1, "foo_pos_1");
$container->param(Bar::class, 2, "bar_pos_2");

$container->setter(FooInterface::class, "setHoge", $container->lazy(function(){return new Hoge;}));
$container->setter(BarInterface::class, "setPiyo", $container->lazyCallable(function(){return new Piyo;}));


$container->set("foo", $container->lazyNew(Foo::class));
$container->set("bar", $container->lazyNew(Bar::class));

var_dump(
    $container->get("foo"),
    $container->get("bar")
);



