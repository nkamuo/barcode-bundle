# Barcode Bundle for Symfony

The **Barcode Bundle** is a custom Symfony library that provides a complete and flexible solution for generating, processing, encoding, decoding, and formatting barcodes and QR codes. It is built on top of popular third-party libraries like `chillerlan/php-qrcode`, `picqer/php-barcode-generator`, and `endroid/qr-code`.

## Table of Contents

1. [Installation](#installation)
2. [Features](#features)
3. [Configuration](#configuration)
4. [Usage](#usage)
    - [Formatters](#formatters)
    - [Encoders](#encoders)
    - [Decoders](#decoders)
    - [Generators](#generators)
5. [Extending the Library](#extending-the-library)
6. [Testing](#testing)
7. [Compiler Pass](#compiler-pass)
8. [Contributing](#contributing)

---

## 1. Installation

This library is designed to work seamlessly with Symfony applications and Symfony Flex.

### Install via Composer

Add the library to your Symfony application:

```bash
composer require nkamuo/barcode-bundle
```

This will:
- Register the `Nkamuo\Barcode\BarcodeBundle` in `config/bundles.php`.
- Copy the default configuration to `config/packages/barcode.yaml`.
- If not,  you can add them manually as follow
```php
[
   //...
   Nkamuo\Barcode\BarcodeBundle::class => ['all' => true]
]
```

### Requirements

- PHP 8.1 or higher.
- Symfony 6.0 or later.
- Composer 2.0 or later.

---

## 2. Features

The Barcode Bundle allows you to:
- Generate Barcodes, DataMatrix and QR codes.
- Encode arbitrary data into a variety of formats.
- Decode barcodes and QR codes.
- Apply custom formatting to barcodes.
- Chain multiple formatters, encoders, etc. for complex workflows.
- Bind components like formatters, encoders, and decoders to specific processors for maximum flexibility.

---

## 3. Configuration


*3.1 Basic Usage*
---

**3.1.1 - Create the Barcode Processor**
```php


use Endroid\QrCode\Writer\ConsoleWriter;
use Nkamuo\Barcode\BarcodeProcessor;
use Nkamuo\Barcode\Decoder\ChainBarcodeDecoder;
use Nkamuo\Barcode\Decoder\GS1\GS1ComplexBarcodeDecoder;
use Nkamuo\Barcode\Decoder\GS1\GS1SimpleBarcodeDecoder;
use Nkamuo\Barcode\Encoder\ChainBarcodeEncoder;
use Nkamuo\Barcode\Encoder\GS1\GS1ComplexBarcodeEncoder;
use Nkamuo\Barcode\Factory\BarcodeFactory;
use Nkamuo\Barcode\Formatter\ChainBarcodeFormatter;
use Nkamuo\Barcode\Formatter\GS1\ReadableLabelBarcodeFormatter;
use Nkamuo\Barcode\Formatter\BarcodeFormatter;
use Nkamuo\Barcode\Encoder\GS1\GS1QRCodeEncoder;
use Nkamuo\Barcode\Formatter\GS1\DataBarcodeFormatter;
use Nkamuo\Barcode\Generator\ChainBarcodeGenerator;
use Nkamuo\Barcode\Generator\SerialNumberBarcodeGenerator;
use Nkamuo\Barcode\Repository\InMemoryBarcodeRepository;
use Nkamuo\Barcode\Sequence\SequenceGenerator;
use Nkamuo\Barcode\Storage\InMemoryHashStorage;
use Picqer\Barcode\Renderers\PngRenderer;
use Picqer\Barcode\Types\TypeCode128;




$factory = new BarcodeFactory();

$repository = new InMemoryBarcodeRepository();

$formatter = new ChainBarcodeFormatter(formatters: [
    new DataBarcodeFormatter(),
    new ReadableLabelBarcodeFormatter(),
    new BarcodeFormatter(),
]);

$encoder = new ChainBarcodeEncoder(
    encoders: [
        new GS1QRCodeEncoder(
            writer: new ConsoleWriter(),//new PngWriter(),
            formatter: $formatter,
        ),
        new GS1ComplexBarcodeEncoder(
            encoder: new TypeCode128(),
            renderer: new PngRenderer(),//new PngWriter(),
            formatter: $formatter,

        ),
    ]
);

$decoder = new ChainBarcodeDecoder(
    decoders: [
        new GS1ComplexBarcodeDecoder(),
        new GS1SimpleBarcodeDecoder(),
    ],
    factory: $factory,
);


$generator = new ChainBarcodeGenerator(
    generators: [
        new SerialNumberBarcodeGenerator(
            sequenceGenerator: new SequenceGenerator(
                storage: new InMemoryHashStorage(),
            ),
        ),
    ],
    factory: $factory,
);


$processor = new BarcodeProcessor(
    factory: $factory,
    encoder: $encoder,
    decoder: $decoder,
    generator: $generator,
    formatter: $formatter,
    repository: $repository,
);



return $processor;

```


**3.1.2 Use the processor** 

- Generate Barcode
```php

/** @var BarcodeProcessorInterface */
$processor = require __DIR__. '/processor.php';

$barcode = $processor->generate(
    context: [
        'prefix' => 'RM-',
        'format' => 'png',
        'width' => 300,
        'height' => 300,
        'error_correction_level' => 'high',
        // Other relevant options for your generator
    ]
);

echo $barcode;
```
- output
```cmd
RM-0000001
```

- Decode Barcode
```php

/** @var BarcodeProcessorInterface  $processor*/
$processor = require __DIR__. '/processor.php';
$barcode = $processor->decode(
    data: ']d201034531200000111719112510ABCD1234'
//    symbol: 'DataMatrix',
    context: [
        'standard' => 'GS1',
    ]
);

echo $barcode;
echo $barcode->getAttribute('01')->getValue();
```

- Encode Barcode
```php
<?php
/** @var BarcodeProcessorInterface  $processor*/
$processor = require __DIR__. '/processor.php';
$result = $processor->encode(
    barcode: $barcode,
    symbol: 'Code128',
    format: 'PNG',
    context: [
        'standard' => 'GS1',
    ]
);

?>
<img src="<?php echo $result;?>"/>

```

**3.2 With Symfony**

By default, the bundle comes with a configuration file located at `config/packages/barcode.yaml`. You can customize the following settings:

```yaml
# config/packages/barcode.yaml
barcode:
    default_storage: 'in_memory'             # Default storage for barcodes
    enabled_formatters: ['qrcode', 'barcode'] # List of formatters to enable
    processors:
        default:                              # Default processor chain
            encoders: ['basic_encoder']
            decoders: ['default_decoder']
            formatters: ['default_formatter']
        custom_processor:                     # Example of a custom processor
            encoders: ['advanced_encoder']
            decoders: ['special_decoder']
            formatters: ['special_formatter']
```

### Default Storage

`default_storage` defines the storage mechanism (e.g., `in_memory` for temporary storage).

### Processors

Processors define how components like formatters, encoders, and decoders are chained together. You can have multiple processors with different configurations.


## 4. Usage

The library is built around a processor model, where components like formatters, encoders, decoders, and generators can be combined into chains.

### Formatters

Formatters are responsible for modifying or formatting barcode data.

#### Example Usage

```php
use Nkamuo\Barcode\Formatter\ChainBarcodeFormatter;

$formatter = $container->get(ChainBarcodeFormatter::class);
$formatted = $formatter->format("Some Data", ["option1" => true]);
```

> You can extend formatters by tagging your custom services with `barcode.formatter`.

### Encoders

Encoders convert raw data into barcodes or QR codes. You can use multiple encoders in chainable workflows.

#### Example Usage

```php
use Nkamuo\Barcode\Encoder\ChainBarcodeEncoder;

$encoder = $container->get(ChainBarcodeEncoder::class);
$encodedData = $encoder->encode("Some Data");
```

Encoders are tagged with `barcode.encoder`.

### Decoders

Decoders recognize and extract information from barcodes or QR codes.

#### Example Usage

```php
use Nkamuo\Barcode\Decoder\ChainBarcodeDecoder;

$decoder = $container->get(ChainBarcodeDecoder::class);
$decoded = $decoder->decode($barcodeData);
```

Decoders are tagged with `barcode.decoder`.

### Generators

Generators are responsible for creating visual representations of barcodes or QR codes.

#### Example Usage

```php
use Nkamuo\Barcode\Generator\BarcodeGenerator;

$generator = $container->get(BarcodeGenerator::class);
$image = $generator->generate('Some Data', ['size' => 200, 'type' => 'qrcode']);
```

---

## 5. Extending the Library

The library uses Symfony's tagged service system to allow easy extension of formatters, encoders, decoders, and processors.

### Adding a Custom Formatter

Create a new formatter that implements `FormatterInterface`:

```php
namespace App\Formatter;

use Nkamuo\Barcode\Formatter\FormatterInterface;

class CustomFormatter implements FormatterInterface
{
    public function format(string $data, array $context = []): string
    {
        // Custom formatting logic
        return strtoupper($data);
    }
}
```

Register your formatter in the service container:

```yaml
# config/services.yaml
services:
    App\Formatter\CustomFormatter:
        tags:
            - { name: 'barcode.formatter', processor: 'default' }
```

---

## 6. Testing

The library includes unit tests located in the `tests/simple` folder. These tests cover all core functionality, such as formatters, encoders, and decoders.

### Running Tests

To run the tests, execute:

```bash
composer install
vendor/bin/phpunit tests/simple
```

#### Example Test File

Here is an example of a simple test for a formatter:

```php
<?php

namespace Tests\Simple;

use Nkamuo\Barcode\Formatter\ChainBarcodeFormatter;
use PHPUnit\Framework\TestCase;

class ChainBarcodeFormatterTest extends TestCase
{
    public function testFormatterWorksCorrectly(): void
    {
        $formatter = new ChainBarcodeFormatter();
        $result = $formatter->format('test-data');

        $this->assertEquals('test-data', $result);
    }
}
```

---

## 7. Compiler Pass

The library includes a custom Symfony compiler pass (`BarcodeChainCompilerPass`) to dynamically collect and inject tagged services (formatters, encoders, etc.) into their respective chain services.

### Example Compiler Pass Setup

The compiler pass dynamically injects services tagged with `barcode.{type}` into the relevant chain class, such as `ChainBarcodeFormatter` or `ChainBarcodeEncoder`.

Example tags include:
- `barcode.formatter`
- `barcode.encoder`
- `barcode.decoder`

---

## 8. Contributing

Contributions are welcome! Follow these steps to contribute to the project:

1. Fork the repository on GitHub.
2. Clone your forked repository:

   ```bash
   git clone https://github.com/your-username/barcode-bundle.git
   cd barcode-bundle
   ```

3. Create a new branch:

   ```bash
   git checkout -b my-feature
   ```

4. Make your changes, write tests, and commit them.
5. Push your branch and open a pull request.

We follow the PSR-12 coding standard.

---

## License

This barcode library is licensed under the **MIT License**. See the [LICENSE](LICENSE) file for more details.

---

### Final Notes

This library provides an extensible way to manage barcodes in Symfony applications, leveraging the full power of Symfony's service container and tagged services system to ensure flexibility. If you encounter any issues, please open an issue on GitHub.

Enjoy using the Barcode Bundle! 😊