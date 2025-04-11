<?php

require_once __DIR__ . '/../../vendor/autoload.php';


$parserConfig = new \Lamoda\GS1Parser\Parser\ParserConfig();
$parser = new \Lamoda\GS1Parser\Parser\Parser($parserConfig);

$validatorConfig = new \Lamoda\GS1Parser\Validator\ValidatorConfig();
$validator = new \Lamoda\GS1Parser\Validator\Validator($parser, $validatorConfig);

$value = ']d4719512002889';
// $value = ']d201034531200000111719112510ABCD1234';

$resolution = $validator->validate($value);

if ($resolution->isValid()) {
    // ...
} else {
    var_dump($resolution->getErrors());
}