<?php

namespace Sicet7\Container\Base\Interfaces;

use DI\Definition\Source\MutableDefinitionSource;

interface PluginInterface
{
    public function register(MutableDefinitionSource $source): void;
}