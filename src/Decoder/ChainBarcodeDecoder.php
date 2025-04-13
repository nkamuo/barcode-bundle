<?php
namespace Nkamuo\Barcode\Decoder;

use Nkamuo\Barcode\Factory\BarcodeFactoryInterface;
use Nkamuo\Barcode\Model\BarcodeInterface;
use Nkamuo\Barcode\Model\WritableBarcodeInterface;

class ChainBarcodeDecoder implements BarcodeDecoderInterface{

    public function __construct(
        private readonly iterable $decoders,
        private readonly BarcodeFactoryInterface $factory,
    ){}
    /**
     * @inheritDoc
     */
    public function decode(WritableBarcodeInterface $barcode, string $data, string|null $symbol = null, string|null $format = null, array $context = []): BarcodeInterface
    {
        foreach ($this->decoders as $decoder) {
            if ($decoder->supports($data, $symbol, $format, $context)) {
                $barcode2 = $this->factory->createWritable($context);
                $result = $decoder->decode($barcode2, $data, $symbol, $format, $context);
                // if ($result->getStandard() !== $barcode->getStandard()) {
                //     throw new \InvalidArgumentException("The decoded barcode standard does not match the expected standard");
                // }
                return $result->copyWith(

                );
            }
        }

        throw new \InvalidArgumentException("No suitable decoder found for the given barcode data");
    }
    
    /**
     * @inheritDoc
     */
    public function getSupportedFormats(): array {
        return [];
    }
    
    /**
     * @inheritDoc
     */
    public function getSupportedStandards(): array {
        return [];
    }
    
    /**
     * @inheritDoc
     */
    public function getSupportedSymbols(): array {
        return [];
    }
    
    /**
     * @inheritDoc
     */
    public function supports(string $data, string|null $symbol = null, string|null $format = null, array $context = []): bool {

        foreach ($this->decoders as $decoder) {
            if ($decoder->supports($data, $symbol, $format, $context)) {
                return true;
            }
        }

        return false;
    }
}