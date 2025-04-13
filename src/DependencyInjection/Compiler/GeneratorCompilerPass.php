<?php

declare(strict_types=1);

namespace Nkamuo\Barcode\DependencyInjection\Compiler;

use Nkamuo\Barcode\Generator\ChainBarcodeGenerator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class GeneratorCompilerPass implements CompilerPassInterface
{
    public const TAG_NAME = 'barcode.generator';

    public function process(ContainerBuilder $container): void
    {

        $processors = [];

        $taggedServices = $container->findTaggedServiceIds(self::TAG_NAME);

            foreach ($taggedServices as $id => $tags) {
                foreach ($tags as $attributes) {
                    $processor = $attributes['processor'] ?? 'default';
                    $chainGeneratorId = sprintf('barcode.generator.%s', $processor);
                    $processors[$chainGeneratorId] ??= [];
                    $generators = $processors[$chainGeneratorId]['generators'] ?? [];
                    $generators = [...$generators, new Reference($id)];
                    $processors[$chainGeneratorId]['generators'] = $generators;
                }
            }

            foreach ($processors as $id => $config) {
                $generators = $config['generators'];
                // in this method you can manipulate the service container:
                // for example, changing some container service:
                if(!$container->hasDefinition($id)) {
                    $container->setDefinition($id, new Definition(
                        class: ChainBarcodeGenerator::class,
                    ));
                }
                $definition = $container->getDefinition($id);
                $definition
                        ->setArgument('generators', $generators);
            }
    }
}
