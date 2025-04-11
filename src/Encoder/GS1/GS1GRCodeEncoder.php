<?php
namespace Nkamuo\Barcode\Encoder\GS1;

use chillerlan\QRCode\QRCode;
use Nkamuo\Barcode\Encoder\BarcodeEncoderInterface;
use Nkamuo\Barcode\Model\BarcodeInterface;

class GS1GRCodeEncoder implements BarcodeEncoderInterface{


    private QRCode $qrCode;

    public function __construct(?QRCode $qrCode = null) {
        $this->qrCode = $qrCode ?? new QRCode();
    }
    
    /**
     * @inheritDoc
     */
    public function encode(BarcodeInterface $barcode, string $symbol, string|null $format = null, array $context = []): string {
        if(!$this->supports($barcode, $symbol, $format, $context)){
            throw new \InvalidArgumentException("Unsupported barcode or symbol");
        }
        
        $value = $barcode->getValue();
        $standard = $barcode->getStandard();
        $type = $barcode->getType();
        $attributes = $barcode->getAttributes();
        $metadata = $barcode->getMetadata();
        $data = $this->qrCode->render($$value, );
        return $data;
    }
    
    /**
     * @inheritDoc
     */
    public function supports(BarcodeInterface $barcode, string $symbol, string|null $format = null, array $context = []): bool {
        // Check if the barcode is a GS1 barcode
        if ($barcode->getStandard() !== 'GS1') {
            return false;
        }

        // Check if the symbol is supported
        $supportedSymbols = ['QR', 'EAN-13', 'UPC', 'Code128'];
        if (!in_array($symbol, $supportedSymbols)) {
            return false;
        }

        // Check if the format is supported
        $supportedFormats = ['PNG', 'SVG', 'PDF'];
        if ($format !== null && !in_array($format, $supportedFormats)) {
            return false;
        }

        return true;
    }
}