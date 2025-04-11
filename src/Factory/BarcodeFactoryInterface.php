<?php
namespace Nkamuo\Barcode\Factory;

use Nkamuo\Barcode\Model\BarcodeInterface;
use Nkamuo\Barcode\Model\WritableBarcodeInterface;

interface BarcodeFactoryInterface{

    public function create(array $context = []): BarcodeInterface;
    public function createWritable(array $context = []): WritableBarcodeInterface;
}