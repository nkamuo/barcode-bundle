<?php

declare(strict_types=1);

namespace Nkamuo\Barcode\DependencyInjection\Compiler;


use Nkamuo\Barcode\Decoder\ChainBarcodeDecoder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DecoderCompilerPass implements CompilerPassInterface
{
    public const TAG_NAME = 'barcode.decoder';

    public const FACTORY_SERVICE_ID = 'barcode.factory.default';

    public function process(ContainerBuilder $container): void
    {

        $processors = [];

        $taggedServices = $container->findTaggedServiceIds(self::TAG_NAME);

            foreach ($taggedServices as $id => $tags) {
                foreach ($tags as $attributes) {
                    $processor = $attributes['processor'] ?? 'default';
                    $chainDecoderId = sprintf('barcode.decoder.%s', $processor);
                    $processors[$chainDecoderId] ??= [];
                    $decoders = $processors[$chainDecoderId]['decoders'] ?? [];
                    $decoders = [...$decoders, new Reference($id)];
                    $processors[$chainDecoderId]['decoders'] = $decoders;
                }
            }


            foreach ($processors as $id => $config) {
                $decoders = $config['decoders'];
                // in this method you can manipulate the service container:
                // for example, changing some container service:
                if(!$container->hasDefinition($id)) {
                    $container->setDefinition($id, new Definition(
                        class: ChainBarcodeDecoder::class,
                    ));
                }
                $definition = $container->getDefinition($id);
                $definition
                        ->setArgument('decoders', $decoders)
                        ->setArgument('factory', new Reference(self::FACTORY_SERVICE_ID))
                ;
            }
    }
}
