<?php
namespace Nkamuo\Barcode\Model;



interface WritableBarcodeInterface extends BarcodeInterface{

    /**
     * Sets the barcode value.
     *
     * @param string $value The new barcode value.
     * @return self
     */
    public function setValue(string $value): self;


    /**
     * Sets the barcode standard. GS1, EAN,  etc.
     * @param string|null $standard The new barcode standard type.
     * @return self
     */
    public function setStandard(?string $standard): self;

    /**
     * Sets the barcode type. Eg GTIN, SSCC, GLN, etc.
     *
     * @param string $type The new barcode type.
     * @return self
     */
    public function setType(string $type): self;


    /**
     * Sets the barcode symbol this was scanned from  - EG, gs1_datamatrix, ean, gs1_qrcode.
     *
     * @param string|null $symbol The new attribute symbols.
     * @return self
     */
    public function setSymbol(?string $symbol): self;

   
    /**
     * 
     * Adds a new barcode attribute.
     * 
     * @param string $code
     * @param string $value
     * @param mixed $label
     * @param mixed $type
     * @param array $metadata
     * @return WritableBarcodeAttributeInterface
     */
    public function addAttribute(
        string $code,
        string $value,
        ?string $label = null,
        ?string $type = null,
        array $metadata = []
    ): WritableBarcodeAttributeInterface;

    /**
     * Removes a barcode attribute by its code.
     *
     * @param string $code The code of the attribute to remove.
     * @return self
     */
    public function removeAttribute(string $code): self;

    /**
     * Sets the barcode attributes.
     *
     * @param array<int,BarcodeAttributeInterface> $attributes The new barcode attributes.
     * @return self
     */

     /**
      * Adds a new metadata entry to the barcode.
      * @param string $key
      * @param mixed $value
      * @return self
      */
     public function addMetadata(string $key, mixed $value): self;

    /**
     * Removes a metadata entry from the barcode.
     * @param string $key
     * @return self
     */
    public function removeMetadata(string $key): self;


    /**
     * Sets the barcode metadata.
     *
     * @internal It's recommended not to use this method directly.
     * @see BarcodeInterface::addMetadata()
     * @see BarcodeInterface::removeMetadata()
     * @param array $metadata The new barcode metadata.
     * @return self
     */
    public function setMetadata(array $metadata): self;
}