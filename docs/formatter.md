#### Barcode Formatters

The formatters provide a way to convert a `BarcodeInterface` instance into a specific format, such as a human-readable label, a GS1-compliant string, or other custom formats. Formatters are useful for presenting barcode data in a way that meets specific requirements or standards.

---

### Example: Using a ReadableLabelBarcodeFormatter

The `ReadableLabelBarcodeFormatter` is an implementation of the `BarcodeFormatterInterface` that formats barcode data into a human-readable label.

```php
$formatter = new ReadableLabelBarcodeFormatter();

$formattedData = $formatter->format(
    barcode: $barcode, // An instance of BarcodeInterface
    format: 'LABEL',   // Optional format type
    context: []        // Additional context
);

echo $formattedData; // Outputs: (01) 01234567890128 (10) ABCD1234
```

---

### Use the ChainBarcodeFormatter

In a large application, you can combine multiple formatter implementations using the `ChainBarcodeFormatter`. This allows you to handle various formatting requirements seamlessly.

```php
$formatter = new ChainBarcodeFormatter(
    formatters: [
        new ReadableLabelBarcodeFormatter(),
        new DataBarcodeFormatter(),
        new CustomBarcodeFormatter(), // Your custom formatter implementation
    ]
);

$formattedData = $formatter->format(
    barcode: $barcode,
    format: 'GS1',
    context: ['symbol' => 'GS1-128']
);

echo $formattedData; // Outputs a GS1-compliant string or other formatted data
```

The `ChainBarcodeFormatter` iterates through the registered formatters and uses the first one that supports the given barcode and format.

---

### Custom Formatters

All formatters must implement the `BarcodeFormatterInterface` interface and override the `supports` and `format` methods.

```php
interface BarcodeFormatterInterface {
    public function format(
        BarcodeInterface $barcode,
        ?string $format = null,
        array $context = []
    ): string;

    public function supports(
        BarcodeInterface $barcode,
        ?string $format = null,
        array $context = []
    ): bool;
}
```

---

### Example: Custom Formatter Implementation

Here is an example of a custom formatter that supports a specific barcode format and standard.

```php
readonly class CustomBarcodeFormatter implements BarcodeFormatterInterface {
    public function format(
        BarcodeInterface $barcode,
        ?string $format = null,
        array $context = []
    ): string {
        // Custom formatting logic
        $value = $barcode->getValue();
        $type = $barcode->getType();
        $standard = $barcode->getStandard();

        return sprintf('[%s] %s (%s)', $standard, $value, $type);
    }

    public function supports(
        BarcodeInterface $barcode,
        ?string $format = null,
        array $context = []
    ): bool {
        // Check if the formatter supports the given barcode and format
        return $barcode->getStandard() === 'CUSTOM_STANDARD' && $format === 'CUSTOM_FORMAT';
    }
}
```

---

### Supported Formats, Standards, and Symbols

Each formatter implementation specifies the formats, standards, and symbols it supports. For example:

#### ReadableLabelBarcodeFormatter
- **Supported Formats**: `LABEL`
- **Supported Standards**: `GS1`
- **Supported Symbols**: `Code128`, `QRCode`, `DataMatrix`, ...

#### DataBarcodeFormatter
- **Supported Formats**: `GS1`
- **Supported Standards**: `GS1`, `EAN`, `UPC`, 
- **Supported Symbols**: `GS1-128`, `GS1-Databar`, `GS1-DataMatrix`, `GS1-QR`

#### ChainBarcodeFormatter
The `ChainBarcodeFormatter` dynamically supports the formats, standards, and symbols of its child formatters.

---

### Error Handling

If no suitable formatter is found for the given barcode, the `ChainBarcodeFormatter` throws an exception:

```php
throw new \InvalidArgumentException('No suitable formatter found for the barcode.');
```

Ensure that your formatters are registered in the correct order to handle specific cases before falling back to more generic formatters.

---

### Example: Using DataBarcodeFormatter for GS1

The `DataBarcodeFormatter` is designed to format GS1-compliant barcodes into a string suitable for encoding or printing.

```php
$formatter = new DataBarcodeFormatter();

$formattedData = $formatter->format(
    barcode: $barcode,
    format: 'GS1',
    context: ['symbol' => 'GS1-128']
);

echo $formattedData; // Outputs: ]C10101234567890128<GS>10ABCD1234
```

This formatter ensures that the data adheres to GS1 standards, including the use of FNC1 prefixes and group separators.

---

This documentation provides an overview of how to use, extend, and implement barcode formatters in your application. For more advanced use cases, refer to the specific formatter implementations in the source code.