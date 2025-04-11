<?php

require_once __DIR__ . '/../../vendor/autoload.php';


$gs1AIs = ([
    '01' => 'Global Trade Item Number (GTIN)',
    '02' => 'Product Number',
    '10' => 'Batch or Lot Number',
    '11' => 'Production Date',
    '12' => 'Due Date',
    '13' => 'Packaging Date',
    '15' => 'Best Before Date',
    '17' => 'Expiration Date',
    '20' => 'Item Reference',
    // Add more AIs as needed
    '21' => 'Serial Number',
    '22' => 'Consumer Product Code',
    '30' => 'Price',
    '37' => 'Count of Items',
    '400' => 'Country of Origin',
    '410' => 'Shipping Container Code',
    // Assets - Returnable and other types

]);
// $gs1AIKeys = array_keys($gs1AIs);
$gs1AIKeys = array_map('strval', array_keys($gs1AIs));
// var_dump($gs1AIKeys);

$config = new \Lamoda\GS1Parser\Parser\ParserConfig();


$config
    ->setFnc1SequenceRequired(false)
    ->setKnownAIs($gs1AIKeys);
$parser = new \Lamoda\GS1Parser\Parser\Parser($config);

// $value = '4719512002889';
$value = ']d201034531200000111719112510ABCD1234';

$barcode = $parser->parse($value);

var_dump($barcode->ais());
// var_dump($barcode);

var_dump($barcode->type());


// $barcode is an object of Barcode class