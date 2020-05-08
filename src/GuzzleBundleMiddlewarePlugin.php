<?php

namespace Wizbit\Bundle\GuzzleBundleMiddlewarePlugin;

use EightPoints\Bundle\GuzzleBundle\PluginInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class GuzzleBundleMiddlewarePlugin extends Bundle implements PluginInterface
{
    public function getPluginName(): string
    {
        return 'middleware';
    }

    public function addConfiguration(ArrayNodeDefinition $pluginNode): void
    {
        $pluginNode
            ->beforeNormalization()
            ->ifNotInArray(['enabled'])
                ->then(static function($value) {
                    return ['middleware' => $value];
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

    public function load(array $configs, ContainerBuilder $container) : void
    {
    }

    public function loadForClient(array $config, ContainerBuilder $container, string $clientName, Definition $handler) : void
    {
        if (true === $config['enabled']) {
            foreach ($config['middleware'] as $middleware) {
                $handler->addMethodCall('push', $this->parseCallable($middleware));
            }
        }
    }

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
