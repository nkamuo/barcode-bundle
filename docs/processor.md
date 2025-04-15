#### Barcode Processor

The `BarcodeProcessor` is the central orchestrator that integrates all the major components of the barcode system. It provides a unified interface for generating, encoding, decoding, and searching barcodes. By combining the functionality of factories, encoders, decoders, formatters, and repositories, the processor simplifies complex workflows and ensures seamless interaction between components.

---

### Purpose of the Processor

The processor acts as a high-level abstraction that coordinates the following operations:
1. **Barcode Generation**: Creates new barcodes based on a given context.
2. **Barcode Encoding**: Converts a barcode into a specific format (e.g., QR code, PNG, SVG).
3. **Barcode Decoding**: Interprets raw barcode data and converts it into a `BarcodeInterface` instance.
4. **Barcode Searching**: Searches for barcodes in the repository based on decoded data.

By delegating these tasks to the appropriate components, the processor ensures modularity and reusability.

---

### Example: Using the Barcode Processor

The processor can be used to handle all barcode-related operations in a single workflow.

```php
use Nkamuo\Barcode\BarcodeProcessor;
use Nkamuo\Barcode\Factory\BarcodeFactory;
use Nkamuo\Barcode\Repository\InMemoryBarcodeRepository;
use Nkamuo\Barcode\Formatter\ChainBarcodeFormatter;
use Nkamuo\Barcode\Encoder\ChainBarcodeEncoder;
use Nkamuo\Barcode\Decoder\ChainBarcodeDecoder;
use Nkamuo\Barcode\Generator\ChainBarcodeGenerator;

// Initialize components
$factory = new BarcodeFactory();
$repository = new InMemoryBarcodeRepository();
$formatter = new ChainBarcodeFormatter([...]); // Add formatters
$encoder = new ChainBarcodeEncoder([...]);     // Add encoders
$decoder = new ChainBarcodeDecoder([...]);     // Add decoders
$generator = new ChainBarcodeGenerator([...]); // Add generators

// Create the processor
$processor = new BarcodeProcessor(
    factory: $factory,
    encoder: $encoder,
    decoder: $decoder,
    generator: $generator,
    formatter: $formatter,
    repository: $repository
);

// Generate a new barcode
$barcode = $processor->generate(['type' => 'GTIN', 'value' => '0123456789012']);
echo $barcode->getValue(); // Outputs: 0123456789012

// Encode the barcode into a QR code
$encoded = $processor->encode($barcode, 'QR', 'PNG');
file_put_contents('barcode.png', $encoded);

// Decode raw barcode data
$decodedBarcode = $processor->decode('0101234567890128', 'EAN-13');
echo $decodedBarcode->getValue(); // Outputs: 0101234567890128

// Search for barcodes in the repository
$results = $processor->search('0101234567890128');
foreach ($results as $result) {
    echo $result->getValue();
}
```

---

### Processor Interface

The `BarcodeProcessorInterface` defines the contract for any processor implementation. This ensures that custom processors can be created while adhering to the same interface.

```php
interface BarcodeProcessorInterface {
    public function generate(array $context = []): BarcodeInterface;
    public function decode(string $data, ?string $symbol = null, ?string $format = null, array $context = []): BarcodeInterface;
    public function encode(BarcodeInterface $barcode, string $symbol, ?string $format = null, array $context = []): string;
    public function search(string $data, array $context = []): array;
}
```

---

### Relation to Other Components

The processor integrates the following components:

1. **Factory**:
   - The processor uses the `BarcodeFactory` to create new `BarcodeInterface` or `WritableBarcodeInterface` instances during generation or decoding.

2. **Encoders**:
   - The processor delegates encoding tasks to the `BarcodeEncoderInterface` implementations (e.g., `GS1QRCodeEncoder`, `ChainBarcodeEncoder`) to convert barcodes into specific formats.

3. **Decoders**:
   - The processor uses `BarcodeDecoderInterface` implementations (e.g., `GS1SimpleBarcodeDecoder`, `ChainBarcodeDecoder`) to interpret raw barcode data and populate barcode objects.

4. **Formatters**:
   - The processor relies on `BarcodeFormatterInterface` implementations (e.g., `ReadableLabelBarcodeFormatter`, `ChainBarcodeFormatter`) to format barcodes into human-readable or standard-compliant strings.

5. **Generators**:
   - The processor uses `BarcodeGeneratorInterface` implementations (e.g., `SerialNumberBarcodeGenerator`, `ChainBarcodeGenerator`) to create new barcodes with unique values or attributes.

6. **Repositories**:
   - The processor interacts with the `BarcodeRepositoryInterface` (e.g., `InMemoryBarcodeRepository`, `DBALBarcodeRepository`) to persist, retrieve, or search for barcodes.

---

### Example: Processor in a Symfony Application

In a Symfony application, the processor and its dependencies can be configured as services in the `services.yaml` file.

```yaml
services:
  barcode.processor.default:
    class: Nkamuo\Barcode\BarcodeProcessor
    public: true
    arguments:
      $factory: "@barcode.factory.default"
      $encoder: "@barcode.encoder.default"
      $decoder: "@barcode.decoder.default"
      $generator: "@barcode.generator.default"
      $formatter: "@barcode.formatter.default"
      $repository: "@barcode.repository.default"
```

This configuration ensures that the processor is fully integrated with the other components and can be injected into controllers or services.

---

### Error Handling

The processor handles errors by throwing exceptions specific to the operation being performed:
1. **BarcodeGenerationException**: Thrown when barcode generation fails.
2. **BarcodeEncodeException**: Thrown when encoding fails.
3. **BarcodeDecodeException**: Thrown when decoding fails.
4. **InvalidArgumentException**: Thrown when unsupported operations are attempted (e.g., unsupported barcode type or format).

---

### Benefits of Using the Processor

1. **Centralized Workflow**: The processor simplifies workflows by providing a single entry point for all barcode-related operations.
2. **Modularity**: By delegating tasks to individual components, the processor ensures that each component remains focused on its specific responsibility.
3. **Extensibility**: New encoders, decoders, formatters, generators, or repositories can be added without modifying the processor itself.
4. **Consistency**: The processor ensures that all operations are performed using standardized barcode objects created by the factory.

---

This documentation provides an overview of how to use, extend, and integrate the `BarcodeProcessor` in your application. For more advanced use cases, refer to the specific implementations of the components it interacts with.