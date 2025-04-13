<?php


namespace Nkamuo\Barcode\Encoder;
use Nkamuo\Barcode\Exception\BarcodeEncodeException;
use Nkamuo\Barcode\Model\BarcodeInterface;

/**
 * BarcodeEncoderInterface defines the contract for encoding barcodes.
 *
 * This interface provides methods to encode a barcode into a specific symbol and format
 * Implementations of this interface should handle the specifics of the encoding process.
 */

interface BarcodeEncoderInterface{

    /**
     * Encodes the given barcode into a specific format.
     *
     * @param BarcodeInterface $barcode The barcode to encode.
     * @param string $symbol The symbol to use for encoding (e.g., "QR", "EAN-13", etc.).
     * @param string|null $format The format to use for encoding (e.g., "PNG", "SVG", etc.).
     * @param array $context Optional metadata to include in the encoding process.
     * @return string The encoded barcode.
     * @throws BarcodeEncodeException
     */
    public function encode(BarcodeInterface $barcode, string $symbol, ?string $format = null, array $context = []): string;

    /**
     * Checks if the encoder supports the given barcode and symbol.
     * @param BarcodeInterface $barcode
     * @param string $symbol The symbol to check support for (e.g., "QR", "EAN-13", etc.).
     * @param string|null $format The format to check support for (e.g., "PNG", "SVG", etc.).
     * @param array $context Optional metadata to include in the check.
     * @return bool True if the encoder supports the barcode and symbol, false otherwise.
     */
    public function supports(BarcodeInterface $barcode, string $symbol, ?string $format = null, array $context = []): bool;


}