<?php

namespace Sicet7\Plugin\Container\Interfaces;

use Sicet7\Plugin\Container\MutableDefinitionSourceHelper;

interface PluginInterface
{
    public function register(MutableDefinitionSourceHelper $source): void;
}