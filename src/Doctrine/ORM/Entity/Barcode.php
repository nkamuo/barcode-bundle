<?php
namespace Nkamuo\Barcode\Doctrine\ORM\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nkamuo\Barcode\Model\WritableBarcodeAttributeInterface;
use Nkamuo\Barcode\Model\WritableBarcodeInterface;


#[ORM\Entity]
#[ORM\Table(name: 'barcode')]
#[ORM\Index(name: 'barcode_code_idx', columns: ['code'])]
class Barcode implements WritableBarcodeInterface{

    #[ORM\Id]
    #[ORM\Column(type: 'ulid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $code = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $standard = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $symbol = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $value = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $metadata = [];

    #[ORM\OneToMany(mappedBy: 'barcode', targetEntity: BarcodeAttribute::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $attributes;


    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $format = null;

    /**
     * @inheritDoc
     */
    public function addAttribute(string $code, string $value, string|null $label = null, string|null $type = null, array $metadata = []): WritableBarcodeAttributeInterface {
        $attribute = new BarcodeAttribute($this,);
        $attribute
            ->setCode($code)
            ->setValue($value)
            ->setLabel($label)
            ->setType($type)
            ->setMetadata($metadata);
        $this->attributes->add($attribute);
        return $attribute;
    }
    
    /**
     * @inheritDoc
     */
    public function addMetadata(string $key, mixed $value): self {
        $this->metadata[$key] = $value;
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function removeAttribute(string $code): self {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getCode() === $code) {
                $this->attributes->removeElement($attribute);
                return $this;
            }
        }
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function removeMetadata(string $key): self {
        unset($this->metadata[$key]);
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setMetadata(array $metadata): self {
        $this->metadata = $metadata;
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setStandard(string|null $type): self {
        $this->standard = $type;
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setType(string $type): self {
        $this->type = $type;
        return $this;
    }


    /** @inheritDoc */
    public function getSymbol(): ?string {
        return $this->symbol;
    }

     /**
     * @inheritDoc
     */
    public function setSymbol(?string $symbol): self {
        $this->symbol = $symbol;
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setValue(string $value): self {
        $this->value = $value;
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getAttributes(): array {
        return $this->attributes->toArray();
    }
    
    /**
     * @inheritDoc
     */
    public function getMetadata(string|null $key = null): mixed {
        if ($key === null) {
            return $this->metadata;
        }
        return $this->metadata[$key] ?? null;
    }
    
    /**
     * @inheritDoc
     */
    public function getStandard(): string|null {
        return $this->standard;
    }
    
    /**
     * @inheritDoc
     */
    public function getType(): string {
        return $this->type;
    }
    
    /**
     * @inheritDoc
     */
    public function getValue(): string {
        return $this->value;
    }
}