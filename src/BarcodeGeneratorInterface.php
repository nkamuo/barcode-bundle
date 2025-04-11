<?php
namespace Nkamuo\Barcode;

use Nkamuo\Barcode\Model\BarcodeInterface;

interface BarcodeGeneratorInterface{

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
    
}