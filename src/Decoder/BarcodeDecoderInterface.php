<?php
namespace Nkamuo\Barcode\Decoder;

use Nkamuo\Barcode\Model\WritableBarcodeInterface;

interface BarcodeDecoderInterface{

    /**
     * Checks if the decoder supports the given barcode data.
     * @param string $data The barcode data to check.
     * @param string|null $symbol The symbol to check support for (e.g., "QR", "EAN-13", etc.).
     * @param string|null $format The format to check support for (e.g., "PNG", "SVG", "STRING" etc.).
     * @param array $context
     * @return void
     */
    public function supports(string $data, ?string $symbol = null, ?string $format = null, array $context = []): bool;
   
    /**
     * Summary of decode
     * @param WritableBarcodeInterface $barcode 
     * @param string $data
     * @param string|null $symbol
     * @param string|null $format
     * @param array $context
     * @return void
     */
    public function decode(WritableBarcodeInterface $barcode, string $data, ?string $symbol = null, ?string $format = null, array $context = []): void;

     
    /**
     * Returns the supported barcode formats.
     *
     * @return array An array of supported barcode formats.
     */
    public function getSupportedFormats(): array;


    /**
     * Returns the supported barcode standards.
     *
     * @return array An array of supported barcode standards.
     */
    public function getSupportedStandards(): array;

    /**
     * Returns the supported barcode types.
     *
     * @return array An array of supported barcode types.
     */
    public function getSupportedSymbols(): array;
}