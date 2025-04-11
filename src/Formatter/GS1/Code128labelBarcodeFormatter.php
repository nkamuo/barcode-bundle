<?php
namespace Nkamuo\Barcode\Formatter\GS1;

use Nkamuo\Barcode\Formatter\BarcodeFormatterInterface;
use Nkamuo\Barcode\Model\BarcodeInterface;

class Code128labelBarcodeFormatter implements BarcodeFormatterInterface{
    /**
     * @inheritDoc
     */
    public function format(BarcodeInterface $barcode, ?string $format = null, array $context = []): string {
        
        $ais = $barcode->getAttributes();
        $formattedData = '';

        foreach ($ais as $ai) {
            if($formattedData !== '') {
                $formattedData .= ' ';
            }
            $formattedData .= sprintf("(%s) %s", $ai->getCode(), $$ai->getValue());
        }

        return trim($formattedData);
    }

    /**
     * @inheritDoc
     */
    public function supports(BarcodeInterface $barcode, ?string $format = null, array $context = []): bool {
        return $barcode->getType() === 'Code128' && $barcode->getStandard() === 'GS1';
    // You can also check the format and context if needed
    }
}