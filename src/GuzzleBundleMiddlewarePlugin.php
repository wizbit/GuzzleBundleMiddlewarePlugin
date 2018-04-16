<?php

namespace Wizbit\Bundle\GuzzleBundleCachePlugin;

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GuzzleBundleMiddlewarePlugin extends Bundle implements EightPointsGuzzleBundlePlugin
{
    /**
     * The name of this plugin. It will be used as the configuration key.
     *
     * @return string
     */
    public function getPluginName(): string
    {
        return 'middleware';
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $pluginNode
     *
     * @return void
     */
    public function addConfiguration(ArrayNodeDefinition $pluginNode)
    {
        $pluginNode
            ->canBeEnabled()
            ->children()
            ->end()
        ;
    }

    /**
     * Load this plugin: define services, load service definition files, etc.
     *
     * @param array                                                   $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
    }

    /**
     * Add configuration nodes for this plugin to the provided node.
     *
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string                                                  $clientName
     * @param \Symfony\Component\DependencyInjection\Definition       $handler
     *
     * @return void
     */
    public function loadForClient(array $config, ContainerBuilder $container, string $clientName, Definition $handler)
    {
        if (true === $config['enabled']) {
//            $handler->addMethodCall('push', [$forwardHeaderMiddlewareExpression, $this->getPluginName()]);
        }
    }
}
