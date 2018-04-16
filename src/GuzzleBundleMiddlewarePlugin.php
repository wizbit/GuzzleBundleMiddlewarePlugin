<?php

namespace Wizbit\Bundle\GuzzleBundleCachePlugin;

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
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
            ->beforeNormalization()
            ->ifNotInArray(['enabled'])
                ->then(function($value) {
                    return array('middleware' => $value);
                })
            ->end()
            ->canBeEnabled()
            ->children()
                ->arrayNode('middleware')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->variablePrototype()
                        ->validate()
                            ->ifTrue(function ($v) {
                                return false === is_string($v) && (false === is_array($v) || count($v) !== 2);
                            })
                            ->thenInvalid('You must specify a callable')
                        ->end()
                    ->end()
                ->end()
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
            foreach ($config['middleware'] as $middleware) {
                $handler->addMethodCall('push', $this->parseCallable($middleware));
            }
        }
    }

    /**
     * Parses a callable.
     *
     * @param string|array $callable A callable
     *
     * @return string|array A parsed callable
     */
    private function parseCallable($callable)
    {
        if (is_string($callable)) {
            if ('' !== $callable && '@' === $callable[0]) {
                throw new InvalidArgumentException(sprintf('The value must be the id of the service without the "@" prefix (replace "%s" with "%s").', $callable, substr($callable, 1)));
            }

            if (false !== strpos($callable, ':')) {

                if (false === strpos($callable, '::'))  {
                    $parts = explode(':', $callable);
                    return array(new Reference($parts[0]), $parts[1]);
                } else {
                    return $callable;
                }
            }

            return array(new Reference($callable), '__invoke');
        }

        if (is_array($callable)) {
            if (isset($callable[0]) && isset($callable[1])) {
                return array(new Reference($callable[0]), $callable[1]);
            }

            throw new InvalidArgumentException(sprintf('Middleware must contain an array with two elements. Check your YAML syntax.'));
        }

        throw new InvalidArgumentException(sprintf('Middleware must be a string or an array. Check your YAML syntax.'));
    }
}
