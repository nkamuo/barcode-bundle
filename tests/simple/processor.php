<?php


require __DIR__ . '/../../vendor/autoload.php';



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


$generatory = new ChainBarcodeGenerator(
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
    generator: $generatory,
    formatter: $formatter,
    repository: $repository,
);



return $processor;
