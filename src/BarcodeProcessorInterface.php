<?php
namespace Nkamuo\Barcode;

use Nkamuo\Barcode\Model\BarcodeInterface;

interface BarcodeProcessorInterface{

     /**
     * Generates a new Barcode instance from the provided context/configuration.
     *
     * The context may include parameters such as standard type,
     * initial identifier value, attribute definitions, metadata, etc.
     *
     * @param array $context The configuration or context needed to generate the barcode.
     * @return BarcodeInterface
     */
    public function generate(array $context = []): BarcodeInterface;


     /**
     * Summary of decode
     * @param string $data
     * @param string|null $symbol
     * @param string|null $format
     * @param array $context
     * @return BarcodeInterface 
     */
    public function decode(string $data, ?string $symbol = null, ?string $format = null, array $context = []): BarcodeInterface;

     
        /**
     * Encodes the given barcode into a specific format.
     *
     * @param BarcodeInterface $barcode The barcode to encode.
     * @param string $symbol The symbol to use for encoding (e.g., "QR", "EAN-13", etc.).
     * @param string|null $format The format to use for encoding (e.g., "PNG", "SVG", etc.).
     * @param array $context Optional metadata to include in the encoding process.
     * @return string The encoded barcode.
     */
    public function encode(BarcodeInterface $barcode, string $symbol, ?string $format = null, array $context = []): string;

    /**
     * Searches for data based on the provided string and context.
     *
     * @param string $data The string to search for.
     * @param array $context An optional array of context to refine the search.
     * @return array<int,BarcodeInterface> An array containing the search results.
     */
    public function search(string $data, array $context = []): array;

    
}