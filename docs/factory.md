#### Barcode Factory

The `BarcodeFactory` is responsible for creating instances of `BarcodeInterface` and `WritableBarcodeInterface`. It acts as a central component for generating barcode objects that can be used across the system by other components such as encoders, decoders, formatters, and generators.

---

### Purpose of the Factory

The factory provides a consistent way to create barcode objects, ensuring that all components in the system (e.g., processors, encoders, decoders, generators) work with standardized barcode instances. This is particularly useful when working with complex workflows where barcodes need to be created, modified, or processed dynamically.

---

### Example: Creating a Barcode Instance

The `BarcodeFactory` can create both immutable (`BarcodeInterface`) and writable (`WritableBarcodeInterface`) barcode objects.

```php
$factory = new BarcodeFactory();

// Create a standard BarcodeInterface instance
$barcode = $factory->create([
    'value' => '0123456789012',
    'type' => 'GTIN',
]);

// Create a WritableBarcodeInterface instance
$writableBarcode = $factory->createWritable([
    'value' => '0123456789012',
    'type' => 'GTIN',
]);

echo $writableBarcode->getValue(); // Outputs: 0123456789012
```

---

### Relation to Other Components

The `BarcodeFactory` is a foundational component that interacts with other documented components in the following ways:

1. **Encoders**:
   - Encoders like `GS1QRCodeEncoder` and `ChainBarcodeEncoder` rely on `BarcodeInterface` instances created by the factory to encode barcodes into specific formats (e.g., QR codes, Data Matrix).

2. **Decoders**:
   - Decoders like `GS1SimpleBarcodeDecoder` and `ChainBarcodeDecoder` use `WritableBarcodeInterface` instances created by the factory to populate barcode data after decoding raw input.

3. **Formatters**:
   - Formatters like `ReadableLabelBarcodeFormatter` and `DataBarcodeFormatter` work with `BarcodeInterface` instances created by the factory to format barcode data into human-readable or GS1-compliant strings.

4. **Generators**:
   - Generators like `SerialNumberBarcodeGenerator` and `ChainBarcodeGenerator` use `WritableBarcodeInterface` instances from the factory to generate new barcodes with unique values or attributes.

5. **Processors**:
   - Processors act as orchestrators, combining the functionality of encoders, decoders, formatters, and generators. The factory ensures that processors have access to standardized barcode objects for seamless integration.

---

### Factory Interface

The `BarcodeFactoryInterface` defines the contract for any factory implementation. This ensures that custom factories can be created while adhering to the same interface.

```php
interface BarcodeFactoryInterface {
    public function create(array $context = []): BarcodeInterface;
    public function createWritable(array $context = []): WritableBarcodeInterface;
}
```

---

### Customizing the Factory

You can implement your own factory by extending the `BarcodeFactoryInterface`. This allows you to customize how barcodes are created, such as adding default values, validation, or additional metadata.

```php
class CustomBarcodeFactory implements BarcodeFactoryInterface {
    public function create(array $context = []): BarcodeInterface {
        // Custom logic for creating a BarcodeInterface instance
        return new Barcode(
            value: $context['value'] ?? '',
            type: $context['type'] ?? 'CUSTOM_TYPE'
        );
    }

    public function createWritable(array $context = []): WritableBarcodeInterface {
        // Custom logic for creating a WritableBarcodeInterface instance
        return new Barcode(
            value: $context['value'] ?? '',
            type: $context['type'] ?? 'CUSTOM_TYPE'
        );
    }
}
```

---

### Error Handling

The factory itself does not perform validation on the context provided. It is the responsibility of the components using the factory (e.g., encoders, decoders, generators) to validate the barcode data.

---

### Example: Using the Factory in a Processor

The factory is often used in processors to create barcodes dynamically during workflows.

```php
$factory = new BarcodeFactory();
$processor = new BarcodeProcessor($factory, $encoder, $decoder, $formatter);

$barcode = $processor->process([
    'value' => '0123456789012',
    'type' => 'GTIN',
]);

echo $barcode->getValue(); // Outputs: 0123456789012
```

---

This documentation provides an overview of how to use, extend, and integrate the `BarcodeFactory` in your application. For more advanced use cases, refer to the specific implementations in the source code.