<?php

namespace Sicet7\Plugin\Container\Factories;

use DI\DependencyException;
use Invoker\ParameterResolver\ParameterResolver;

final readonly class AutowireFactory
{
    public function __construct(
        private ParameterResolver $resolver
    ) {
    }

    /**
     * @param string $class
     * @param array $parameters
     * @return mixed
     * @throws DependencyException
     */
    public function make(
        string $class,
        array $parameters = []
    ): mixed {
        try {
            if (!method_exists($class, '__construct')) {
                return new $class();
            }
            $args = $this->resolver->getParameters(
                new \ReflectionMethod($class, '__construct'),
                $parameters,
                []
            );
            ksort($args);
            return new $class(...$args);
        } catch (\ReflectionException $exception) {
            throw new DependencyException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}