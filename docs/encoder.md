#### Barcode Encoders

The encoders provide a way to convert an instance of `BarcodeInterface` into an Electronic Data Interchange (EDI) format, such as a barcode, data matrix, QR code, or RFID, depending on the implementations available to you.

```php
$formatter = new DataBarcodeFormatter();

$encoder = new GS1QRCodeEncoder(
    writer: new ConsoleWriter(), // or new PngWriter(),
    formatter: $formatter,
);
```

---

### Use the ChainBarcodeEncoder

In a large application, it can be useful to create several implementations of the `BarcodeEncoderInterface` and wrap them together using the `ChainBarcodeEncoder` class.

```php
$encoder = new ChainBarcodeEncoder(
    encoders: [
        new GS1QRCodeEncoder(
            writer: new ConsoleWriter(),
            formatter: $formatter,
        ),
        new GS1ComplexBarcodeEncoder(
            encoder: new TypeCode128(),
            renderer: new PngRenderer(),
            formatter: $formatter,
        ),
    ]
);
```

When using this as part of a Symfony application, the chain encoder is automatically set up for you and injected into the processor class.  
You can register your custom encoder implementations and tag them with `barcode.encoder`.

```yaml
# config/services.yaml
services:
    App\Service\Barcode\Encoder\RFIDEncoder:
        tags:
            - name: barcode.encoder     # Binds this to the default processor
              priority: 12
            - name: barcode.encoder
              processor: gs1            # You can also specify the processor to bind this encoder to
```

---

### Custom Encoders

All encoders must implement the `BarcodeEncoderInterface` interface and override the `supports` and `encode` methods.

```php
interface BarcodeEncoderInterface {
    public function encode(
        BarcodeInterface $barcode,
        string $symbol,
        ?string $format = null,
        array $context = []
    ): string;

    public function supports(
        BarcodeInterface $barcode,
        string $symbol,
        ?string $format = null,
        array $context = []
    ): bool;
}
```

#### Example: ANSI Data Matrix Encoder

```php
readonly class ANSIDataMatrixEncoder implements BarcodeEncoderInterface {
    public const STANDARD_NAME = 'ANSI';

    public function __construct(
        private readonly BarcodeFormatterInterface $formatter,
        private readonly SomeANSIBarcodeGenerator $generator,
    ) {}

    public function encode(
        BarcodeInterface $barcode,
        string $symbol,
        ?string $format = null,
        array $context = []
    ): string {
        $data = $this->formatter->format($barcode, $context);
        /** @var string $result */
        $result = $this->generator->generateDataMatrix($data);
        return $result;
    }

    public function supports(
        BarcodeInterface $barcode,
        string $symbol,
        ?string $format = null,
        array $context = []
    ): bool {
        // Check if the barcode is an ANSI barcode or supports non-standard barcodes
        if (($standard = $barcode->getStandard()) && strtoupper($standard) !== self::STANDARD_NAME) {
            return false;
        }

        // Check if the symbol is supported
        $supportedSymbols = ['DataMatrix'];
        if (!in_array(strtoupper($symbol), $supportedSymbols)) {
            return false;
        }

        // Check if the format is supported
        $supportedFormats = ['PNG', 'SVG', 'PDF'];
        if ($format !== null && !in_array(strtoupper($format), $supportedFormats)) {
            return false;
        }

        return true;
    }
}
```

The `supports` method is called by the `ChainBarcodeEncoder` to decide which encoder to use for a given `BarcodeInterface` instance.  
It checks the encoders in the order they were provided, starting with the first entry. Make sure to specify the child encoders in the correct order, leaving the default encoders as the last entries.

When using this as a Symfony bundle, you can achieve this ordering with Symfony's `priority` tag key, as shown above.