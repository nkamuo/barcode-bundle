<?php
namespace Nkamuo\Barcode\Formatter;

use Nkamuo\Barcode\Model\BarcodeInterface;

class ChainBarcodeFormatter implements BarcodeFormatterInterface{

    public function __construct(
        /** @var iterable<BarcodeFormatterInterface> */
        private readonly iterable $formatters,
    ) {
    }
    /**
     * @inheritDoc
     */
    public function format(BarcodeInterface $barcode, string|null $format = null, array $context = []): string {
        foreach ($this->formatters as $formatter) {
            if ($formatter->supports($barcode, $format, $context)) {
                return $formatter->format($barcode, $format, $context);
            }
        }
        throw new \InvalidArgumentException('No suitable formatter found for the barcode.');
    }
    
    /**
     * @inheritDoc
     */
    public function supports(BarcodeInterface $barcode, string|null $format = null, array $context = []): bool {
        foreach ($this->formatters as $formatter) {
            if ($formatter->supports($barcode, $format, $context)) {
                return true;
            }
        }
        return false;
    }
}