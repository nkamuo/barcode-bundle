# Barcode Bundle for Symfony and Standalone Applications

The **Barcode Bundle** is a flexible and extensible library for generating, processing, encoding, decoding, and formatting barcodes, DataMatrix and QR codes. It is designed to work seamlessly with Symfony applications but can also be used in standalone PHP projects. The library aims to support established standards such as `GS1` and `ANSI` — either out of the box or through third-party and custom extensions.

---

## Table of Contents

1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Quick Start](#quick-start)
    - [Using in Symfony](#using-in-symfony)
    - [Using in Standalone PHP Applications](#using-in-standalone-php-applications)
4. [Configuration](#configuration)
    - [Symfony Configuration](#symfony-configuration)
    - [Standalone Configuration](#standalone-configuration)
5. [Components Overview](#components-overview)
    - [Encoders](#encoders)
    - [Decoders](#decoders)
    - [Formatters](#formatters)
    - [Generators](#generators)
    - [Repositories](#repositories)
    - [Processors](#processors)
6. [Extending the Library](#extending-the-library)
7. [Customization](#customization)
8. [Examples](#examples)
9. [Testing](#testing)
10. [Contributing](#contributing)
11. [Credits](#credits)
12. [License](#license)

---

## 1. Introduction

The Barcode Bundle provides a complete solution for working with barcodes and QR codes. It supports:
- Generating barcodes and QR codes.
- Encoding data into various formats.
- Decoding barcodes and QR codes into structured data.
- Formatting barcodes for human-readable or standard-compliant outputs.
- Extending and customizing components like formatters, encoders, decoders, and processors.

Whether you're building a Symfony application or a standalone PHP project, this library is designed to be modular, extensible, and easy to use.

---

## 2. Installation

### Requirements
- PHP 8.1 or higher.
- Symfony 6.0 or later (for Symfony integration).
- Composer 2.0 or later.

### Install via Composer

To install the library, run:

```bash
composer require nkamuo/barcode-bundle
```

For Symfony applications, this will:
- Register the `Nkamuo\Barcode\BarcodeBundle` in `config/bundles.php`.
- Copy the default configuration to `config/packages/barcode.yaml`.

If the bundle is not automatically registered, add it manually:

```php
// config/bundles.php
return [
    // ...
    Nkamuo\Barcode\BarcodeBundle::class => ['all' => true],
];
```

---

## 3. Quick Start

### Using in Symfony

1. **Install the library** (see [Installation](#installation)).
2. **Configure the bundle** in `config/packages/barcode.yaml` (see [Symfony Configuration](#symfony-configuration)).
3. **Use the Barcode Processor**:

```php
use Nkamuo\Barcode\BarcodeProcessorInterface;

/** @var BarcodeProcessorInterface $processor */
$processor = $container->get(BarcodeProcessorInterface::class);

// Generate a barcode
$barcode = $processor->generate(['type' => 'GTIN', 'value' => '0123456789012']);
echo $barcode->getValue(); // Outputs: 0123456789012

// Encode the barcode into a QR code
$encoded = $processor->encode($barcode, 'QR', 'PNG');
file_put_contents('barcode.png', $encoded);

// Decode a barcode
$decodedBarcode = $processor->decode('0101234567890128', 'EAN-13');
echo $decodedBarcode->getValue(); // Outputs: 0101234567890128
```

### Using in Standalone PHP Applications

1. **Install the library** (see [Installation](#installation)).
2. **Set up the components manually**:

```php
require __DIR__ . '/vendor/autoload.php';

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

// Use the processor
$barcode = $processor->generate(['type' => 'GTIN', 'value' => '0123456789012']);
echo $barcode->getValue();
```

---

## 4. Configuration

### Symfony Configuration

The default configuration file is located at `config/packages/barcode.yaml`. You can customize the following settings:

```yaml
barcode:
    default_storage: 'in_memory'             # Default storage for barcodes
    enabled_formatters: ['qrcode', 'barcode'] # List of formatters to enable
    processors:
        default:                              # Default processor chain
            encoders: ['basic_encoder']
            decoders: ['default_decoder']
            formatters: ['default_formatter']
```

### Standalone Configuration

For standalone applications, you can configure components manually by instantiating them and passing the required dependencies (see [Quick Start](#using-in-standalone-php-applications)).

---

## 5. Components Overview

### [Encoders](docs/encoder.md)
Encoders convert `BarcodeInterface` instances into specific formats like QR codes, Data Matrix, or PNG images.  
See the [Encoders Documentation](docs/encoder.md) for more details.

### [Decoders](docs/decoder.md)
Decoders interpret raw barcode data and convert it into structured `BarcodeInterface` instances.  
See the [Decoders Documentation](docs/decoder.md) for more details.

### [Formatters](docs/formatter.md)
Formatters provide a way to format barcode data into human-readable or standard-compliant strings.  
See the [Formatters Documentation](docs/formatter.md) for more details.

### [Generators](docs/generator.md)
Generators create new barcodes based on specific contexts or configurations.  
See the [Generators Documentation](docs/generator.md) for more details.

### [Repositories](docs/repository.md)
Repositories manage the persistence and retrieval of barcodes.  
See the [Repositories Documentation](docs/repository.md) for more details.

### [Processors](docs/processor.md)
Processors orchestrate the interaction between all components, providing a unified interface for barcode operations.  
See the [Processors Documentation](docs/processor.md) for more details.

---

## 6. Extending the Library

The library is designed to be extensible. You can add custom formatters, encoders, decoders, and processors by implementing the respective interfaces.  
Refer to the [Extending Documentation](docs) for detailed guides on how to extend each component.

---

## 7. Customization

You can customize the library by:
- Adding custom formatters, encoders, decoders, or generators.
- Creating custom processors to handle specific workflows.
- Implementing your own repository for barcode storage (e.g., database-backed).

---

## 8. Examples

### Generate and Encode a Barcode

```php
$barcode = $processor->generate(['type' => 'GTIN', 'value' => '0123456789012']);
$encoded = $processor->encode($barcode, 'QR', 'PNG');
file_put_contents('barcode.png', $encoded);
```

### Decode a Barcode

```php
$decodedBarcode = $processor->decode('0101234567890128', 'EAN-13');
echo $decodedBarcode->getValue();
```

### Format a Barcode

```php
$formatted = $formatter->format($barcode, 'LABEL');
echo $formatted;
```

---

## 9. Testing

Run the tests using PHPUnit:

```bash
composer install
vendor/bin/phpunit tests/simple
```

---

## 10. Contributing

Contributions are welcome! Please follow these steps:
1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Write tests for your changes.
4. Open a pull request.

Feel free to open issues for bugs or feature requests.

---

## 11. Credits

This library leverages the following third-party libraries:
- [chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode)
- [picqer/php-barcode-generator](https://github.com/picqer/php-barcode-generator)
- [endroid/qr-code](https://github.com/endroid/qr-code)

---

## 12. License

This library is licensed under the **MIT License**. See the [LICENSE](LICENSE) file for more details.

---

Enjoy using the Barcode Bundle! 😊