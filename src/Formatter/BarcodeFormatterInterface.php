<?php
namespace Nkamuo\Barcode\Formatter;

use Nkamuo\Barcode\Model\BarcodeInterface;

interface BarcodeFormatterInterface{

    /**
     * Formats the given barcode into a specific format.
     *
     * @param BarcodeInterface $barcode The barcode to format.
     * @param string|null $format The format to use for formatting.
     * @param array $context Additional context or options for formatting.
     * @return string The formatted barcode.
     */
   public function format(BarcodeInterface $barcode, ?string $format = null, array $context = []): string;

    /**
     * Checks if the formatter supports the given barcode and format.
     *
     * @param BarcodeInterface $barcode The barcode to check.
     * @param string|null $format The format to check support for.
     * @param array $context Additional context or options for checking support.
     * @return bool True if the formatter supports the barcode and format, false otherwise.
     */
    public function supports(BarcodeInterface $barcode, ?string $format = null, array $context = []): bool;
}