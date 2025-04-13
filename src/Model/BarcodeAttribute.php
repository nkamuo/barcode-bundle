<?php

namespace Nkamuo\Barcode\Model;

class BarcodeAttribute implements BarcodeAttributeInterface, WritableBarcodeAttributeInterface
{
    private string $code;
    private ?string $value;
    private ?string $label;
    private ?string $type;
    private array $metadata = [];
    private WritableBarcodeInterface $barcode;

    /**
     * Constructor for BarcodeAttribute.
     *
     * @param string $code
     * @param string|null $value
     * @param string|null $label
     * @param string|null $type
     * @param array $metadata
     * @param WritableBarcodeInterface $barcode
     */
    public function __construct(
        WritableBarcodeInterface $barcode,
        string                   $code,
        ?string                  $value,
        ?string                  $label = null,
        ?string                  $type = null,
        array                    $metadata = [],
    ) {
        $this->code = $code;
        $this->value = $value;
        $this->label = $label;
        $this->type = $type;
        $this->metadata = $metadata;
        $this->barcode = $barcode;
    }

    /**
     * @inheritDoc
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @inheritDoc
     */
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function setValue(mixed $value): self
    {
        if (!is_string($value) && $value !== null) {
            throw new \InvalidArgumentException('The attribute value must be a string or null.');
        }

        $this->value = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @inheritDoc
     */
    public function setLabel(?string $label): self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function setType(?string $type): self
    {
        $this->type = $type;
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
            throw new \RuntimeException("Metadata key '{$key}' not found.");
        }

        return $this->metadata[$key];
    }

    /**
     * @inheritDoc
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Add new metadata to the attribute.
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function addMetadata(string $key, mixed $value): self
    {
        $this->metadata[$key] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function end(): WritableBarcodeInterface
    {
        return $this->barcode;
    }


    public function copyWith(
        ?string $code = null,
        ?string $value = null,
        ?string $label = null,
        ?string $type = null,
        ?array $metadata = null,
    ): self {

        return new self(
            barcode: $this->barcode,
            code: $code ?? $this->code,
            value: $value ?? $this->value,
            label: $label ?? $this->label,
            type: $type ?? $this->type,
            metadata: $metadata ?? $this->metadata,
        );
    }
}