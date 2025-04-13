<?php

namespace Nkamuo\Barcode\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('barcode');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('default_storage')->defaultValue('in_memory')->end()
                ->arrayNode('enabled_formatters')
                    ->scalarPrototype()->end()
                    ->defaultValue(['qrcode', 'barcode'])
                ->end()
            ->end();

        return $treeBuilder;
    }
}