<?php

namespace Nkamuo\Barcode\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

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

    public function process(ContainerBuilder $container): void
    {
        foreach ($this->chainServices as $tag => $serviceClass) {
            // Skip if the chain service does not exist
            if (!$container->hasDefinition($serviceClass)) {
                continue;
            }

            $chainService = $container->getDefinition($serviceClass);

            // Collect tagged services
            $taggedServices = $container->findTaggedServiceIds($tag);

            // Group tagged services by processor key or default
            $servicesByProcessor = [];

            foreach ($taggedServices as $id => $tags) {
                foreach ($tags as $attributes) {
                    $processor = $attributes['processor'] ?? 'default';
                    if (!isset($servicesByProcessor[$processor])) {
                        $servicesByProcessor[$processor] = [];
                    }
                    $servicesByProcessor[$processor][] = new Reference($id);
                }
            }

            // Process each processor group and pass tagged services to constructor
            foreach ($servicesByProcessor as $processor => $serviceReferences) {
                $processorServiceId = sprintf('barcode.processor.%s', $processor);

                // If it's the default processor, use the regular chain service definition
                if ($processor === 'default') {
                    $chainService->setArguments([$serviceReferences]);
                } else {
                    // If a specific processor is defined, create or modify its chain service definition
                    if ($container->hasDefinition($processorServiceId)) {
                        $processorDefinition = $container->getDefinition($processorServiceId);
                        $processorDefinition->setArguments([$serviceReferences]);
                    } else {
                        $container->setDefinition($processorServiceId, new Definition($serviceClass, [$serviceReferences]));
                    }
                }
            }
        }
    }
}