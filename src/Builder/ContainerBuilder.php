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
namespace Fratily\Container\Builder;

/**
 *
 */
class ContainerBuilder implements ContainerBuilderInterface{

    use LazyBuilderTrait;

    /**
     * @var Resolver\Resolver
     */
    private $resolver;

    /**
     * @var object[]|Lazy\LazyInterface[]
     */
    private $services       = [];

    /**
     * @var string[]
     */
    private $taggedServices = [];

    /**
     * Constructor
     *
     * @param   Resolver\Resolver   $resolver
     *  リゾルバ
     */
    public function __construct(Resolver\Resolver $resolver){
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver(){
        return $this->resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getServices(){
        return $this->services;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaggedServicesId(){
        return $this->taggedServices;
    }

    /**
     * {@inheritdoc}
     */
    public function add(
        string $id,
        $service,
        array $tags = [],
        array $types = []
    ){
        if(!is_string($service) && !is_object($service)){
            throw new \InvalidArgumentException();
        }

        if(is_string($service)){
            if(!class_exists($service)){
                throw new \InvalidArgumentException();
            }

            if(!$this->resolver->getClassResolver($service)->getReflection()->isInstantiable()){
                throw new \InvalidArgumentException();
            }

            $service    = new Lazy\LazyNew($service);
        }

        $this->services[$id]    = $service;

        foreach($tags as $tag){
            if(!array_key_exists($tag, $this->taggedServices)){
                $this->taggedServices[$tag] = [];
            }

            $this->taggedServices[$tag][]   = $id;
        }

        foreach($types as $type){
            $this->resolver->addType($type, new Lazy\LazyGet($id));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setScope(string $class, string $scope){
        $this->resolver->getClassResolver($class)
            ->getInstanceGenerator()
            ->setScope($scope)
        ;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addParameter(string $class, $parameter, $value){
        if(!is_string($parameter) || "" === $parameter){
            throw new \InvalidArgumentException();
        }

        if(!is_int($parameter) || 0 > $parameter){
            throw new \InvalidArgumentException();
        }

        if(is_string($parameter)){
            $this->resolver
                ->getClassResolver($class)
                ->addNameParameter($parameter, $value)
            ;
        }elseif(is_int($parameter)){
            $this->resolver
                ->getClassResolver($class)
                ->addPositionParameter($parameter, $value)
            ;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addSetter(string $class, string $setter, $value){
        $this->resolver->getClassResolver($class)->addSetter($setter, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addProperty(string $class, string $property, $value){
        $this->resolver->getClassResolver($class)->addProperty($property, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function parameter(string $class){
        return new ParameterBuilder($this, $class);
    }

    /**
     * {@inheritdoc}
     */
    public function setter(string $class){
        return new SetterBuilder($this, $class);
    }

    /**
     * {@inheritdoc}
     */
    public function property(string $class){
        return new PropertyBuilder($this, $class);
    }
}