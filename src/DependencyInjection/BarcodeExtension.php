<?php

namespace Nkamuo\Barcode\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;

class BarcodeExtension extends Extension
{
    /**
     * Loads bundle configuration and services.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        // Load services configuration from services.yaml
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }


    public function prepend(ContainerBuilder $container)
    {
        // $configs = $container->getExtensionConfig($this->getAlias());
        // $config = $this->processConfiguration(new Configuration(), $configs);


        $paramKey = 'nkamuo.storage.hash.table_name';
        $tableName = 'nkamuo_barcode_hash_table';
        if ($container->hasParameter($paramKey,)) {
            $tableName = $container->getParameter($paramKey);
        }

        $exclude_tables = [$tableName];


        if (!empty($exclude_tables)) {
            $escaped = array_map(function ($table) {
                return preg_quote($table, '~');
            }, $exclude_tables);

            $pattern = '~^(?!(' . implode('|', $escaped) . ')$)~';

            // Prepend the schema_filter config to doctrine
            $container->prependExtensionConfig('doctrine', [
                'orm' => [
                    'schema_filter' => $pattern,
                ]
            ]);
        }
    }
}
