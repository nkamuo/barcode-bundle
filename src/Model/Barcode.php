<?php

namespace Nkamuo\Barcode\Model;

use Nkamuo\Barcode\Exception\BarcodeMetadatKeyNotFound;

class Barcode implements BarcodeInterface, WritableBarcodeInterface
{
    private string $value;
    private ?string $standard = null;
    private string $type;
    private ?string $symbol = null;
    private array $attributes = [];
    private array $metadata = [];

    /**
     * Constructor to initialize a Barcode object.
     *
     * @param string $value
     * @param string $type
     * @param string|null $standard
     * @param string|null $symbol
     * @param array $attributes
     * @param array $metadata
     */
    public function __construct(
        string $value,
        string $type,
        ?string $standard = null,
        ?string $symbol = null,
        array $attributes = [],
        array $metadata = []
    ) {
        $this->value = $value;
        $this->type = $type;
        $this->standard = $standard;
        $this->symbol = $symbol;
        $this->attributes = $attributes;
        $this->metadata = $metadata;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStandard(): ?string
    {
        return $this->standard;
    }

    /**
     * @inheritDoc
     */
    public function setStandard(?string $standard): self
    {
        $this->standard = $standard;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    /**
     * @inheritDoc
     */
    public function setSymbol(?string $symbol): self
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Adds a new attribute to the barcode.
     *
     * @inheritDoc
     */
    public function addAttribute(
        string $code,
        string $value,
        ?string $label = null,
        ?string $type = null,
        array $metadata = []
    ): WritableBarcodeAttributeInterface {
        $attribute = new BarcodeAttribute(code: $code, value: $value, label: $label, type: $type, metadata: $metadata, barcode: $this);
        $this->attributes[] = $attribute;
        return $attribute;
    }

    /**
     * @inheritDoc
     */
    public function removeAttribute(string $code): self
    {
        $this->attributes = array_filter(
            $this->attributes,
            fn($attribute) => $attribute->getCode() !== $code
        );
        return $this;
    }

    /**
     * Sets attributes directly.
     *
     * @param array<int,BarcodeAttributeInterface> $attributes
     * @return self
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata(?string $key = null): mixed
    {
        if ($key === null) {
            return $this->metadata;
        }

        if (!array_key_exists($key, $this->metadata)) {
            throw new BarcodeMetadatKeyNotFound("Metadata key '{$key}' not found.");
        }

        return $this->metadata[$key];
    }

    /**
     * @inheritDoc
     */
    public function addMetadata(string $key, mixed $value): self
    {
        $this->metadata[$key] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeMetadata(string $key): self
    {
        if (!array_key_exists($key, $this->metadata)) {
            throw new BarcodeMetadatKeyNotFound("Metadata key '{$key}' not found.");
        }

        unset($this->metadata[$key]);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }



    public function copyWith(
        ?string $value = null,
        ?string $type = null,
        ?string $standard = null,
        ?string $symbol = null,
        ?array $attributes = null,
        ?array $metadata = null,
    ): self{
        return new self(
            value: $value ?? $this->value,
            type: $type ?? $this->type,
            standard: $standard ?? $this->standard,
            symbol: $symbol ?? $this->symbol,
            attributes: $attributes ?? $this->attributes,
            metadata: $metadata ?? $this->metadata,
        );
    }


    public function __toString(): string
    {
        return $this->getValue();
    }
}