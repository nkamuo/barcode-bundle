<?php
namespace Nkamuo\Barcode\Model;


interface BarcodeAttributeInterface{

    /**
     * Returns the attribute code.
     *
     * @return string
     */
    public function getCode(): string;


    /**
     * Returns the attribute value.
     *
     * @return string|null
     */
    public function getValue(): ?string;


    /**
     * Returns the attribute label.
     *
     * @return string|null
     */
    public function getLabel(): ?string;

    /**
     * Returns the attribute type.
     *
     * @return string
     */
    public function getType(): ?string;

    /**
     * Returns the attribute metadata.
     *
     * @return array|mixed
     */

    public function getMetadata(?string $key = null): mixed;


    /**
     * @param string|null $code
     * @param string|null $value
     * @param string|null $label
     * @param string|null $type
     * @param array|null $metadata
     * @return self
     */
    public function copyWith(
        ?string $code = null,
        ?string $value = null,
        ?string $label = null,
        ?string $type = null,
        ?array $metadata = null,
    ): self;
}