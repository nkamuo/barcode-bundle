<?php
namespace Nkamuo\Barcode\Generator;

use Nkamuo\Barcode\Factory\BarcodeFactoryInterface;
use Nkamuo\Barcode\Generator\BarcodeGeneratorInterface;
use Nkamuo\Barcode\Model\BarcodeInterface;
use Nkamuo\Barcode\Model\WritableBarcodeInterface;

class ChainBarcodeGenerator implements BarcodeGeneratorInterface{
    /**
     * @var iterable<BarcodeGeneratorInterface>
     */
    private iterable $generators;

    public function __construct(iterable $generators,
    private readonly BarcodeFactoryInterface $factory,
    )
    {
        $this->generators = $generators;
    }

    /** @inheritDoc */
    public function generate(WritableBarcodeInterface $barcode, array $context = []): BarcodeInterface
    {
        foreach ($this->generators as $generator) {
            if ($generator->supports($context)) {
                $_barcode = $this->factory->createWritable($context);
                return $generator->generate(barcode: $_barcode, context: $context);
            }
        }
        throw new \InvalidArgumentException('No suitable generator found for the context.');
    }
    

    /** @inheritDoc */
    public function supports(array $context = []): bool
    {
        foreach ($this->generators as $generator) {
            if ($generator->supports($context)) {
                return true;
            }
        }
        return false;
    }
}