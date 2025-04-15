#### Barcode Decoders

The decoders provide a way to interpret barcode data and convert it into an instance of `BarcodeInterface`. This can include decoding barcodes from formats such as QR codes, Data Matrix, or GS1-compliant barcodes.

---

### Example: Using a GS1SimpleBarcodeDecoder

The `GS1SimpleBarcodeDecoder` is an implementation of the `BarcodeDecoderInterface` that decodes GS1-compliant barcodes into a `BarcodeInterface` instance.

```php
$decoder = new GS1SimpleBarcodeDecoder();

$barcode = $decoder->decode(
    barcode: new Barcode(), // An instance of WritableBarcodeInterface
    data: '0101234567890128',       // GS1 barcode data
    symbol: 'EAN-13',               // Barcode symbol
    format: 'STRING',               // Format of the barcode
    context: []                     // Additional context
);

echo $barcode->getValue(); // Outputs: 0101234567890128
```

---

### Use the ChainBarcodeDecoder

In a large application, you can combine multiple decoder implementations using the `ChainBarcodeDecoder`. This allows you to handle various barcode formats and standards easily.

```php
$decoder = new ChainBarcodeDecoder(
    decoders: [
        new GS1SimpleBarcodeDecoder(),
        new GS1ComplexBarcodeDecoder(),
        new CustomBarcodeDecoder(), // Your custom decoder implementation
    ],
    factory: new BarcodeFactory() // A factory for creating writable barcodes
);

$barcode = $decoder->decode(
    barcode: new Barcode(),
    data: ']d201034531200000111719112510ABCD1234',
    symbol: null, //DataMatrix
    format: null,
    context: ['standard' => 'GS1']
);
echo $barcode->getAttribute('01');// Output: 03453120000011 [GTIN]
echo $barcode->getAttribute('10');// Output:  ABCD1234 [Batch Number]
echo $barcode->getAttribute('17');// Output:  191125 [Expiry Date]

```

The `ChainBarcodeDecoder` iterates through the registered decoders and uses the first one that supports the given barcode data.

---

### Custom Decoders

All decoders must implement the `BarcodeDecoderInterface` interface and override the `supports` and `decode` methods.

```php
interface BarcodeDecoderInterface {
    public function supports(
        string $data,
        ?string $symbol = null,
        ?string $format = null,
        array $context = []
    ): bool;

    public function decode(
        WritableBarcodeInterface $barcode,
        string $data,
        ?string $symbol = null,
        ?string $format = null,
        array $context = []
    ): BarcodeInterface;

    public function getSupportedFormats(): array;

    public function getSupportedStandards(): array;

    public function getSupportedSymbols(): array;
}
```

---

### Example: Custom Decoder Implementation

Here is an example of a custom decoder that supports a specific barcode format and standard.

```php
readonly class CustomBarcodeDecoder implements BarcodeDecoderInterface {
    public function supports(
        string $data,
        ?string $symbol = null,
        ?string $format = null,
        array $context = []
    ): bool {
        // Check if the standard is supported
        if (($context['standard'] ?? null) !== 'CUSTOM_STANDARD') {
            return false;
        }

        // Check if the symbol is supported
        $supportedSymbols = ['CUSTOM_SYMBOL'];
        if (!in_array($symbol, $supportedSymbols)) {
            return false;
        }

        // Check if the format is supported
        $supportedFormats = ['STRING'];
        if ($format !== null && !in_array($format, $supportedFormats)) {
            return false;
        }

        return true;
    }

    public function decode(
        WritableBarcodeInterface $barcode,
        string $data,
        ?string $symbol = null,
        ?string $format = null,
        array $context = []
    ): BarcodeInterface {
        if (!$this->supports($data, $symbol, $format, $context)) {
            throw new \InvalidArgumentException("Unsupported barcode data");
        }

        return $barcode
            ->setValue($data)
            ->setType('CUSTOM_TYPE')
            ->setStandard('CUSTOM_STANDARD')
            ->addMetadata('symbol', $symbol)
            ->addMetadata('format', $format);
    }

    public function getSupportedFormats(): array {
        return ['STRING'];
    }

    public function getSupportedStandards(): array {
        return ['CUSTOM_STANDARD'];
    }

    public function getSupportedSymbols(): array {
        return ['CUSTOM_SYMBOL'];
    }
}
```

---

### Supported Formats, Standards, and Symbols

Each decoder implementation specifies the formats, standards, and symbols it supports. For example:

#### GS1SimpleBarcodeDecoder
- **Supported Formats**: `GS1-128`, `GS1-Databar`, `GS1-DataMatrix`, `GS1-QR`, `GS1-UPC`, `GS1-EAN`
- **Supported Standards**: `GS1`, `ISO/IEC 15420`, `ISO/IEC 15424`, `ISO/IEC 15434`, `ISO/IEC 16022`, `ISO/IEC 18004`
- **Supported Symbols**: `GS1-128`, `GS1-Databar`, `GS1-DataMatrix`, `GS1-QR`, `GS1-UPC`, `GS1-EAN`

#### ChainBarcodeDecoder
The `ChainBarcodeDecoder` dynamically supports the formats, standards, and symbols of its child decoders.

---

### Error Handling

If no suitable decoder is found for the given barcode data, the `ChainBarcodeDecoder` throws an exception:

```php
throw new \InvalidArgumentException("No suitable decoder found for the given barcode data");
```

Ensure that your decoders are registered in the correct order to handle specific cases before falling back to more generic decoders.

---

This documentation provides an overview of how to use, extend, and implement barcode decoders in your application. For more advanced use cases, refer to the specific decoder implementations in the source code.