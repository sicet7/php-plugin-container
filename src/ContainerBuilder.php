<?php

namespace Sicet7\Plugin\Container;

use DI\Container;
use DI\ContainerBuilder as DIContainerBuilder;
use DI\Definition\Source\DefinitionArray;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Psr\Container\ContainerInterface;
use Sicet7\Plugin\Container\Factories\AutowireFactory;
use Sicet7\Plugin\Container\Interfaces\PluginInterface;

class ContainerBuilder implements PluginInterface
{
    public const CONTAINER_CLASS = Container::class;

    /**
     * @var PluginInterface[]
     */
    private static array $plugins = [];

    /**
     * @var bool
     */
    private static bool $autowireFactoryRegistration = false;

    /**
     * @param ContainerInterface|null $parentContainer
     * @return ContainerInterface
     * @throws \Exception
     */
    final public static function build(ContainerInterface $parentContainer = null): ContainerInterface
    {
        if (!self::$autowireFactoryRegistration) {
            self::load(new self());
            self::$autowireFactoryRegistration = true;
        }

        $builder = new DIContainerBuilder(self::CONTAINER_CLASS);
        $definitions = new MutableDefinitionSourceHelper(new DefinitionArray());
        foreach (self::$plugins as $plugin) {
            $plugin->register($definitions);
        }
        $builder->addDefinitions($definitions->source);
        $builder->useAttributes(false);
        $builder->useAutowiring(false);
        if ($parentContainer instanceof ContainerInterface) {
            $builder->wrapContainer($parentContainer);
        }
        return $builder->build();
    }

    /**
     * @param PluginInterface $plugin
     * @return void
     */
    final public static function load(PluginInterface $plugin): void
    {
        self::$plugins[] = $plugin;
    }

    private function __construct()
    {
    }

    /**
     * @param MutableDefinitionSourceHelper $source
     * @return void
     */
    public function register(MutableDefinitionSourceHelper $source): void
    {
        $source->factory(
            AutowireFactory::class,
            function(ContainerInterface $container): AutowireFactory {
                return new AutowireFactory(new ResolverChain([
                    0 => new AssociativeArrayResolver(),
                    1 => new TypeHintContainerResolver($container),
                    2 => new DefaultValueResolver(),
                ]));
            }
        );
    }
}