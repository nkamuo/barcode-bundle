<?php
namespace Nkamuo\Barcode\Doctrine\ORM\Entity;

use Nkamuo\Barcode\Exception\BarcodeMetadatKeyNotFound;
use Nkamuo\Barcode\Model\WritableBarcodeAttributeInterface;
use Nkamuo\Barcode\Model\WritableBarcodeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity()]
#[ORM\Table(name: 'barcode_attribute')]
#[ORM\Index(name: 'barcode_attribute_code_idx', columns: ['code'])]
#[ORM\Index(name: 'barcode_attribute_label_idx', columns: ['label'])]
#[ORM\Index(name: 'barcode_attribute_type_idx', columns: ['type'])]
#[ORM\Index(name: 'barcode_attribute_metadata_idx', columns: ['metadata'])]
#[ORM\Index(name: 'barcode_attribute_value_idx', columns: ['value'])]
#[ORM\UniqueConstraint(name: 'barcode_attribute_unique', columns: ['code', 'value'])]
class BarcodeAttribute implements WritableBarcodeAttributeInterface{

    #[ORM\Id]
    #[ORM\Column(type: 'ulid',)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?Ulid $id = null;


    #[ORM\Column(type: 'string', length: 255)]
    private ?string $code = null;


    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $value = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $metadata = [];

    #[ORM\ManyToOne(targetEntity: Barcode::class, inversedBy: 'attributes')]
    #[ORM\JoinColumn(name: 'barcode_id', referencedColumnName: 'id', nullable: false)]
    private Barcode $barcode;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
  
    /**
     * @param Barcode $barcode
     */
    public function __construct(
        WritableBarcodeInterface $barcode,
    ){
        $this->barcode = $barcode;
    }

    /**
     * @inheritDoc
     */
    public function end(): WritableBarcodeInterface {
        return $this->barcode;
    }
    
    /**
     * @inheritDoc
     */
    public function setCode(string $code): self {
        $this->code = $code;
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setLabel(string|null $label): self {
        $this->label = $label;
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
    public function setType(string|null $type): self {
        $this->type = $type;
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setValue(mixed $value): self {
        $this->value = $value;
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getCode(): string {
        return $this->code;
    }
    
    /**
     * @inheritDoc
     */
    public function getLabel(): string|null {
        return $this->label;
    }
    
    /**
     * @inheritDoc
     */
    public function getMetadata(?string $key = null): mixed {
        if($key !== null) {
            if(!array_key_exists($key, $this->metadata)) {
                throw new BarcodeMetadatKeyNotFound(sprintf('Metadata key "%s" not found.', $key));
            }
            return $this->metadata[$key];
        }
        if(empty($this->metadata)) {
            return null;
        }
        return $this->metadata;
    }
    
    /**
     * @inheritDoc
     */
    public function getType(): string|null {
        return $this->type;
    }
    
    /**
     * @inheritDoc
     */
    public function getValue(): ?string {
        return $this->value;
    }
}