<?php
namespace Nkamuo\Barcode\Encoder;

use InvalidArgumentException;
use Nkamuo\Barcode\Model\BarcodeInterface;

class ChainBarcodeEncoder implements BarcodeEncoderInterface{


    public function __construct(
        private readonly iterable $encoders,
    ){}
    
   
    /**
     * @inheritDoc
     */
    public function encode(BarcodeInterface $barcode, string $symbol, string|null $format = null, array $context = []): string {
        foreach ($this->encoders as $encoder) {
            if ($encoder->supports($barcode, $symbol, $format, $context)) {
                return $encoder->encode($barcode, $symbol, $format, $context);
            }
        }

        throw new InvalidArgumentException("No suitable encoder found for the given barcode and symbol");
    }
    
    /**
     * @inheritDoc
     */
    public function supports(BarcodeInterface $barcode, string $symbol, string|null $format = null, array $context = []): bool {
        foreach ($this->encoders as $encoder) {
            if ($encoder->supports($barcode, $symbol, $format, $context)) {
                return true;
            }
        }

        return false;
    }
}