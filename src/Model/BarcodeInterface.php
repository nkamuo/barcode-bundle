<?php
namespace Nkamuo\Barcode\Model;

use Nkamuo\Barcode\Exception\BarcodeMetadatKeyNotFound;


interface BarcodeInterface{
    /**
     * Returns the barcode in formatted form.
     *
     * @return string
     */
    public function getValue(): string;


    /**
     * Returns the barcode starndard EG, GS1, ISO, etc.
     * @return string|null
     */
    public function getStandard(): ?string;

    /**
     * Returns the barcode type.
     *
     * @return string
     */
    public function getType(): string;


    /**
     * Returns the barcode symbol - EG, gs1_datamatrix, ean, gs1_qrcode.
     *
     * @return string|null
     */
    public function getSymbol(): ?string;

    /**
     * Returns the barcode attributes.
     *
     * @return array<int,BarcodeAttributeInterface>
     */
    public function getAttributes(): array;

    
    /**
     * Returns the barcode metadata by its key or return all(array) if key is not given.
     * @param mixed $key
     * @return mixed
     * @throws BarcodeMetadatKeyNotFound
     */
    public function getMetadata(?string $key = null): mixed;
    
}