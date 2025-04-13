<?php

namespace Nkamuo\Barcode;

use Nkamuo\Barcode\DependencyInjection\BarcodeExtension;
use Nkamuo\Barcode\DependencyInjection\Compiler\BarcodeChainCompilerPass;
use Nkamuo\Barcode\DependencyInjection\Compiler\DecoderCompilerPass;
use Nkamuo\Barcode\DependencyInjection\Compiler\EncoderCompilerPass;
use Nkamuo\Barcode\DependencyInjection\Compiler\FormatterCompilerPass;
use Nkamuo\Barcode\DependencyInjection\Compiler\FormatterPass;
use Nkamuo\Barcode\DependencyInjection\Compiler\GeneratorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BarcodeBundle extends Bundle
{

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // Register the Formatter compiler pass
        $container->addCompilerPass(new FormatterCompilerPass());
        $container->addCompilerPass(new EncoderCompilerPass());
        $container->addCompilerPass(new DecoderCompilerPass());
        $container->addCompilerPass(new GeneratorCompilerPass());
    }

    protected function getContainerExtensionClass(): string
    {
        return BarcodeExtension::class;
    }
}