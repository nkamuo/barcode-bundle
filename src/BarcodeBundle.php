<?php

namespace Nkamuo\Barcode;

use Nkamuo\Barcode\DependencyInjection\BarcodeExtension;
use Nkamuo\Barcode\DependencyInjection\Compiler\BarcodeChainCompilerPass;
use Nkamuo\Barcode\DependencyInjection\Compiler\FormatterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BarcodeBundle extends Bundle
{

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // Register the Formatter compiler pass
        $container->addCompilerPass(new BarcodeChainCompilerPass());
    }

    protected function getContainerExtensionClass(): string
    {
        return BarcodeExtension::class;
    }
}