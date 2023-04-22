<?php

namespace Sicet7\Plugin\Container;

use DI\Definition\ArrayDefinition;
use DI\Definition\ArrayDefinitionExtension;
use DI\Definition\DecoratorDefinition;
use DI\Definition\EnvironmentVariableDefinition;
use DI\Definition\Exception\InvalidDefinition;
use DI\Definition\FactoryDefinition;
use DI\Definition\InstanceDefinition;
use DI\Definition\ObjectDefinition;
use DI\Definition\Reference;
use DI\Definition\Source\MutableDefinitionSource;
use DI\Definition\StringDefinition;
use DI\Definition\ValueDefinition;
use Sicet7\Plugin\Container\Factories\AutowireFactory;

final readonly class MutableDefinitionSourceHelper
{
    public function __construct(public MutableDefinitionSource $source)
    {
    }

    /**
     * @param string $name
     * @param callable|array|string $factory
     * @param array $parameters
     * @return FactoryDefinition
     */
    public function factory(
        string $name,
        callable|array|string $factory,
        array $parameters = []
    ): FactoryDefinition {
        $def = new FactoryDefinition($name, $factory, $parameters);
        $this->source->addDefinition($def);
        return $def;
    }

    /**
     * @param string $name
     * @param string $target
     * @return Reference
     */
    public function reference(
        string $name,
        string $target
    ): Reference {
        $def = new Reference($target);
        $def->setName($name);
        $this->source->addDefinition($def);
        return $def;
    }

    /**
     * @param string $name
     * @param callable|array|string $factory
     * @param array $parameters
     * @return DecoratorDefinition
     * @throws InvalidDefinition
     */
    public function decorate(
        string $name,
        callable|array|string $factory,
        array $parameters = []
    ): DecoratorDefinition {
        $def = new DecoratorDefinition($name, $factory, $parameters);
        $target = $this->source->getDefinition($name);
        if ($target !== null) {
            $def->setExtendedDefinition($target);
        }
        $this->source->addDefinition($def);
        return $def;
    }

    /**
     * @param string $name
     * @param string $expression
     * @return StringDefinition
     */
    public function string(
        string $name,
        string $expression
    ): StringDefinition {
        $def = new StringDefinition($expression);
        $def->setName($name);
        $this->source->addDefinition($def);
        return $def;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return ValueDefinition
     */
    public function value(
        string $name,
        mixed $value
    ): ValueDefinition {
        $def = new ValueDefinition($value);
        $def->setName($name);
        $this->source->addDefinition($def);
        return $def;
    }

    /**
     * @param string $name
     * @param string $class
     * @return ObjectDefinition
     */
    public function object(
        string $name,
        string $class
    ): ObjectDefinition {
        $def = new ObjectDefinition($name, $class);
        $this->source->addDefinition($def);
        return $def;
    }

    /**
     * @param string $name
     * @param array $values
     * @param bool $override
     * @return ArrayDefinition
     * @throws InvalidDefinition
     */
    public function array(
        string $name,
        array $values,
        bool $override = false
    ): ArrayDefinition {
        $def = ($override ? new ArrayDefinition($values) : new ArrayDefinitionExtension($values));
        $def->setName($name);
        $oldDef = $this->source->getDefinition($name);
        if ($def instanceof ArrayDefinitionExtension && $oldDef !== null) {
            $def->setExtendedDefinition($oldDef);
        }
        $this->source->addDefinition($def);
        return $def;
    }

    /**
     * @param string $name
     * @param string $variableName
     * @param mixed|null $defaultValue
     * @return EnvironmentVariableDefinition
     */
    public function env(
        string $name,
        string $variableName,
        mixed $defaultValue = null
    ): EnvironmentVariableDefinition {
        $optional = func_num_args() === 3;
        $def = new EnvironmentVariableDefinition(
            $variableName,
            $optional,
            $defaultValue
        );
        $def->setName($name);
        $this->source->addDefinition($def);
        return $def;
    }

    /**
     * @param string $name
     * @param string $class
     * @return FactoryDefinition
     */
    public function autowire(
        string $name,
        string $class
    ): FactoryDefinition {
        $def = new FactoryDefinition($name, function (AutowireFactory $factory) use ($class) {
            return $factory->make($class);
        });
        $this->source->addDefinition($def);
        return $def;
    }
}