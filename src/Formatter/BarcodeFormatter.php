<?php
namespace Nkamuo\Barcode\Formatter;

use Nkamuo\Barcode\Model\BarcodeInterface;

class BarcodeFormatter implements BarcodeFormatterInterface
{
    
    /**
     * @inheritDoc
     */
    public function format(BarcodeInterface $barcode, string|null $format = null, array $context = []): string {
        return $barcode->getValue();
    }
    
    /**
     * @inheritDoc
     */
    public function supports(BarcodeInterface $barcode, string|null $format = null, array $context = []): bool {
        return true;
    }
}