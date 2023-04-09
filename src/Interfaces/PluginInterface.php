<?php

namespace Sicet7\Plugin\Container\Interfaces;

use DI\Definition\Source\MutableDefinitionSource;

interface PluginInterface
{
    public function register(MutableDefinitionSource $source): void;
}