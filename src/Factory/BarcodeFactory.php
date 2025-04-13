<?php

namespace Nkamuo\Barcode\Factory;

use Nkamuo\Barcode\Factory\BarcodeFactoryInterface;
use Nkamuo\Barcode\Model\Barcode;
use Nkamuo\Barcode\Model\BarcodeInterface;
use Nkamuo\Barcode\Model\WritableBarcodeInterface;

class BarcodeFactory implements BarcodeFactoryInterface
{

    public function create(array $context = []): BarcodeInterface
    {
        return $this->createWritable($context);
    }

    public function createWritable(array $context = []): WritableBarcodeInterface
    {
        return new Barcode(
            value: '',
            type: ''
        );
    }
}