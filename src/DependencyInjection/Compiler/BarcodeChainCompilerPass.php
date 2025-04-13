<?php

namespace Nkamuo\Barcode\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BarcodeChainCompilerPass implements CompilerPassInterface
{
    private array $chainServices = [
        'barcode.formatter' => 'Nkamuo\Barcode\Formatter\ChainBarcodeFormatter',
        'barcode.encoder' => 'Nkamuo\Barcode\Encoder\ChainBarcodeEncoder',
        'barcode.decoder' => 'Nkamuo\Barcode\Decoder\ChainBarcodeDecoder',
        'barcode.generator' => 'Nkamuo\Barcode\Generator\ChainBarcodeGenerator',
        'barcode.storage' => 'Nkamuo\Barcode\Storage\ChainHashStorage',
        'barcode.repository' => 'Nkamuo\Barcode\Repository\ChainBarcodeRepository',
    ];

    public function process(ContainerBuilder $container)
    {
        foreach ($this->chainServices as $tag => $class) {
            // Process only if the chain class exists
            if (!$container->hasDefinition($class)) {
                continue;
            }

            $chainService = $container->findDefinition($class);
            $taggedServices = $container->findTaggedServiceIds($tag);

            foreach ($taggedServices as $id => $tags) {
                foreach ($tags as $attributes) {
                    // Check for a specific processor in the tag
                    $processor = $attributes['processor'] ?? 'default';

                    // Add the tagged service to the corresponding chain class
                    if ($processor === 'default') {
                        $chainService->addMethodCall('add' . ucfirst(rtrim(explode('.', $tag)[1], 's')), [new Reference($id)]);
                    } else {
                        // Handle services bound to a specific processor
                        $processorService = sprintf('barcode.processor.%s', $processor);
                        if ($container->hasDefinition($processorService)) {
                            $processorDef = $container->findDefinition($processorService);
                            $processorDef->addMethodCall('add' . ucfirst(rtrim(explode('.', $tag)[1], 's')), [new Reference($id)]);
                        }
                    }
                }
            }
        }
    }
}