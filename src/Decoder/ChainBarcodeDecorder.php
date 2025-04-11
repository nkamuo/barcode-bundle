<?php
namespace Nkamuo\Barcode\Decoder;

use Nkamuo\Barcode\Factory\BarcodeFactoryInterface;
use Nkamuo\Barcode\Model\WritableBarcodeInterface;

class ChainBarcodeDecorder implements BarcodeDecoderInterface{

    public function __construct(
        private readonly iterable $decoders,
        private readonly BarcodeFactoryInterface $barcodeFactory,
        private readonly string $defaultDecoder = 'default',
    ){}
    /**
     * @inheritDoc
     */
    public function decode(WritableBarcodeInterface $barcode, string $data, string|null $symbol = null, string|null $format = null, array $context = []): void {
        foreach ($this->decoders as $decoder) {
            if ($decoder->supports($data, $symbol, $format, $context)) {
                $decoder->decode($barcode, $data, $symbol, $format, $context);
                return;
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