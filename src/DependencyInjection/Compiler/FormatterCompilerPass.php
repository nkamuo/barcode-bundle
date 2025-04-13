<?php

declare(strict_types=1);

namespace Nkamuo\Barcode\DependencyInjection\Compiler;

use Nkamuo\Barcode\Formatter\ChainBarcodeFormatter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class FormatterCompilerPass implements CompilerPassInterface
{
    public const TAG_NAME = 'barcode.formatter';

    public function process(ContainerBuilder $container): void
    {

        $processors = [];

        $taggedServices = $container->findTaggedServiceIds(self::TAG_NAME);

            foreach ($taggedServices as $id => $tags) {
                foreach ($tags as $attributes) {
                    $processor = $attributes['processor'] ?? 'default';
                    $chainFormatterId = sprintf('barcode.formatter.%s', $processor);
                    $processors[$chainFormatterId] ??= [];
                    $formatters = $processors[$chainFormatterId]['formatters'] ?? [];
                    $formatters = [...$formatters, new Reference($id)];
                    $processors[$chainFormatterId]['formatters'] = $formatters;
                }
            }

            foreach ($processors as $id => $config) {
                $formatters = $config['formatters'];
                // in this method you can manipulate the service container:
                // for example, changing some container service:
                if(!$container->hasDefinition($id)) {
                    $container->setDefinition($id, new Definition(
                        class: ChainBarcodeFormatter::class,
                    ));
                }
                $definition = $container->getDefinition($id);
                $definition
                        ->setArgument('$formatters', $formatters);
            }
    }
}
