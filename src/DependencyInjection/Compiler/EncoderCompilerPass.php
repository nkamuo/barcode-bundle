<?php

declare(strict_types=1);

namespace Nkamuo\Barcode\DependencyInjection\Compiler;

use Nkamuo\Barcode\Encoder\ChainBarcodeEncoder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class EncoderCompilerPass implements CompilerPassInterface
{
    public const TAG_NAME = 'barcode.encoder';

    public function process(ContainerBuilder $container): void
    {

        $processors = [];

        $taggedServices = $container->findTaggedServiceIds(self::TAG_NAME);

            foreach ($taggedServices as $id => $tags) {
                foreach ($tags as $attributes) {
                    $processor = $attributes['processor'] ?? 'default';
                    $chainEncoderId = sprintf('barcode.encoder.%s', $processor);
                    $processors[$chainEncoderId] ??= [];
                    $encoders = $processors[$chainEncoderId]['encoders'] ?? [];
                    $encoders = [...$encoders, new Reference($id)];
                    $processors[$chainEncoderId]['encoders'] = $encoders;
                }
            }

            foreach ($processors as $id => $config) {
                $encoders = $config['encoders'];
                // in this method you can manipulate the service container:
                // for example, changing some container service:
                if(!$container->hasDefinition($id)) {
                    $container->setDefinition($id, new Definition(
                        class: ChainBarcodeEncoder::class,
                    ));
                }
                $definition = $container->getDefinition($id);
                $definition
                        ->setArgument('$encoders', $encoders);
            }
    }
}
