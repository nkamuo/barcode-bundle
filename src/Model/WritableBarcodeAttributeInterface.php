<?php

namespace Nkamuo\Barcode\Model;


/**
 * Interface for writable barcode attributes.
 *
 * This interface extends the BarcodeAttributeInterface and adds methods
 * for setting the attribute value, type, and metadata.
 */
interface WritableBarcodeAttributeInterface extends BarcodeAttributeInterface{
   
    public function setCode(string $code): self;

    /**
     * Sets the attribute value.
     *
     * @param mixed $value The new attribute value.
     * @return self
     */
    public function setValue(mixed $value): self;


    /**
     * Sets the attribute label.
     * @param string|null $label
     * @return self
     */
    public function setLabel(?string $label): self;

    /**
     * Sets the attribute type.
     *
     * @param string|null $type The new attribute type.
     * @return self
     */
    public function setType(?string $type): self;


    /**
     * Sets the attribute metadata.
     *
     * @param array $metadata The new attribute metadata.
     * @return self
     */
    public function setMetadata(array $metadata): self;


    /**
     * Returns the original barcode object for chainning.
     * @return WritableBarcodeInterface
     */
    public function end(): WritableBarcodeInterface;
    
}