<?php

use Nkamuo\Barcode\BarcodeProcessorInterface;

require __DIR__ . '/../../vendor/autoload.php';

/** @var BarcodeProcessorInterface */
$processor = require __DIR__. '/processor.php';


$barcode = $processor->generate(
    // barcode: '1234567890123',
    context: [
        'prefix' => 'RM-',
        'generator' => 'serial_number',
        'format' => 'png',
        'width' => 300,
        'height' => 300,
        'error_correction_level' => 'high',
    ]
);

// var_dump($barcode->getValue());

$result = $processor->encode(
    barcode: $barcode,
    symbol: 'Code128',
    format: 'png',
    context: [
        'standard' => 'GS1',
        'width' => 300,
        'height' => 300,
        'error_correction_level' => 'high',
    ]
);

echo $result;
