<?php
namespace Nkamuo\Barcode\Generator;

use Nkamuo\Barcode\Exception\BarcodeGenerationException;
use Nkamuo\Barcode\Model\BarcodeInterface;
use Nkamuo\Barcode\Model\WritableBarcodeInterface;

interface BarcodeGeneratorInterface{

    /**
     * Generates a new Barcode instance from the provided context/configuration.
     *
     * The context may include parameters such as standard type,
     * initial identifier value, attribute definitions, metadata, etc.
     * 
     * @param WritableBarcodeInterface $barcode
     * @param array $context
     * @return BarcodeInterface
     * @throws BarcodeGenerationException
     */
    public function generate(WritableBarcodeInterface $barcode, array $context = []): BarcodeInterface;


    /**
     * Checks if the generator supports the given context.
     * This is useful for determining if the generator can handle the provided context.
     * @param array $context
     * @return bool
     */
    public function supports(array $context): bool;
    
}