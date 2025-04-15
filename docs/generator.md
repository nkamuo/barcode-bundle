#### Barcode Generators

The generators provide a way to create new `BarcodeInterface` instances based on specific contexts or configurations. These generators are useful for producing barcodes with unique serial numbers, predefined prefixes, or other custom attributes.

---

### Example: Using a SerialNumberBarcodeGenerator

The `SerialNumberBarcodeGenerator` is an implementation of the `BarcodeGeneratorInterface` that generates barcodes with sequential serial numbers.

```php
$sequenceGenerator = new SequenceGenerator($storage); // Implements HashStorageInterface
$generator = new SerialNumberBarcodeGenerator($sequenceGenerator, [
    'pad_length' => 8,
    'prefix' => 'SN-',
]);

$barcode = $generator->generate(
    barcode: new WritableBarcode(), // An instance of WritableBarcodeInterface
    context: ['prefix' => 'SN-', 'pad_length' => 8]
);

echo $barcode->getValue(); // Outputs: SN-00000001
```

---

### Use the ChainBarcodeGenerator

In a large application, you can combine multiple generator implementations using the `ChainBarcodeGenerator`. This allows you to handle various barcode generation requirements seamlessly.

```php
$generator = new ChainBarcodeGenerator(
    generators: [
        new SerialNumberBarcodeGenerator($sequenceGenerator),
        new CustomBarcodeGenerator(), // Your custom generator implementation
    ],
    factory: new BarcodeFactory() // A factory for creating writable barcodes
);

$barcode = $generator->generate(
    barcode: new WritableBarcode(),
    context: ['type' => 'SEQUENTIAL']
);

echo $barcode->getValue(); // Outputs a barcode value based on the selected generator
```

The `ChainBarcodeGenerator` iterates through the registered generators and uses the first one that supports the given context.

---

### Custom Generators

All generators must implement the `BarcodeGeneratorInterface` interface and override the `supports` and `generate` methods.

```php
interface BarcodeGeneratorInterface {
    public function generate(
        WritableBarcodeInterface $barcode,
        array $context = []
    ): BarcodeInterface;

    public function supports(array $context): bool;
}
```

---

### Example: Custom Generator Implementation

Here is an example of a custom generator that supports a specific barcode type and context.

```php
readonly class CustomBarcodeGenerator implements BarcodeGeneratorInterface {
    public function generate(
        WritableBarcodeInterface $barcode,
        array $context = []
    ): BarcodeInterface {
        $prefix = $context['prefix'] ?? 'CUSTOM-';
        $value = sprintf('%s%s', $prefix, uniqid());

        return $barcode
            ->setValue($value)
            ->setType('CUSTOM_TYPE')
            ->setStandard('CUSTOM_STANDARD');
    }

    public function supports(array $context): bool {
        return ($context['type'] ?? null) === 'CUSTOM_TYPE';
    }
}
```

---

### Supported Contexts and Configurations

Each generator implementation specifies the contexts and configurations it supports. For example:

#### SerialNumberBarcodeGenerator
- **Supported Contexts**:
  - `prefix`: A string to prepend to the generated serial number.
  - `pad_length`: The length to pad the serial number to (default: 8).
- **Default Behavior**:
  - Generates sequential serial numbers starting from 1.
  - Pads the serial number with leading zeros.

#### ChainBarcodeGenerator
The `ChainBarcodeGenerator` dynamically supports the contexts and configurations of its child generators.

---

### Error Handling

If no suitable generator is found for the given context, the `ChainBarcodeGenerator` throws an exception:

```php
throw new \InvalidArgumentException('No suitable generator found for the context.');
```

Ensure that your generators are registered in the correct order to handle specific cases before falling back to more generic generators.

---

### Example: Using SequenceGenerator with SerialNumberBarcodeGenerator

The `SequenceGenerator` is used internally by the `SerialNumberBarcodeGenerator` to manage sequential numbers.

```php
$storage = new ScopedHashStorage(new DBALHashStorage($connection), 'barcode');
$sequenceGenerator = new SequenceGenerator($storage, step: 1, startAt: 100);

$generator = new SerialNumberBarcodeGenerator($sequenceGenerator, [
    'pad_length' => 6,
    'prefix' => 'SEQ-',
]);

$barcode = $generator->generate(
    barcode: new WritableBarcode(),
    context: ['prefix' => 'SEQ-', 'pad_length' => 6]
);

echo $barcode->getValue(); // Outputs: SEQ-000100
```

---

This documentation provides an overview of how to use, extend, and implement barcode generators in your application. For more advanced use cases, refer to the specific generator implementations in the source code.