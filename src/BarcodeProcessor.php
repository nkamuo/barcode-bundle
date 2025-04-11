<?php
namespace Nkamuo\Barcode;

use Nkamuo\Barcode\Decoder\BarcodeDecoderInterface;
use Nkamuo\Barcode\Encoder\BarcodeEncoderInterface;
use Nkamuo\Barcode\Factory\BarcodeFactoryInterface;
use Nkamuo\Barcode\Model\BarcodeInterface;


class BarcodeProcessor  implements BarcodeProcessorInterface{

    
    public function __construct(
        private readonly BarcodeFactoryInterface $barcodeFactory,
        private readonly BarcodeEncoderInterface $encoder,
        private readonly BarcodeDecoderInterface $decoder,
    ){

    }

    /**
     * @inheritDoc
     */
    public function decode( string $data, string|null $symbol = null, string|null $format = null, array $context = []): BarcodeInterface {
        $barcode = $this->barcodeFactory->createWritable($context);
        $this->decoder->decode($barcode, $data, $symbol, $format, $context);
        return $barcode;
    }
    
    /**
     * @inheritDoc
     */
    public function encode(BarcodeInterface $barcode, string $symbol, string|null $format = null, array $context = []): string {
        if(!$this->encoder->supports($barcode, $symbol, $format, $context)){
            throw new \InvalidArgumentException("Unsupported barcode or symbol");
        }
        
        return $this->encoder->encode($barcode, $symbol, $format, $context);
    }
    
    /**
     * @inheritDoc
     */
    public function generate(array $context = []): BarcodeInterface {
        $barcode = $this->barcodeFactory->createWritable($context);
        $symbol = $context['symbol'] ?? null;
        $format = $context['format'] ?? null;
        $data = $context['data'] ?? null;

        if ($data !== null) {
            $this->encoder->encode($barcode, $symbol, $format, $context);
        }

        return $barcode;
    }
}