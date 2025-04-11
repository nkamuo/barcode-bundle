<?php
namespace Nkamuo\Barcode\Encoder\GS1;

use chillerlan\QRCode\QRCode;
use Nkamuo\Barcode\Encoder\BarcodeEncoderInterface;
use Nkamuo\Barcode\Formatter\BarcodeFormatterInterface;
use Nkamuo\Barcode\Model\BarcodeInterface;
use Picqer\Barcode\Renderers\RendererInterface;
use Picqer\Barcode\Types\TypeCode128;
use Picqer\Barcode\Types\TypeInterface;

class GS1ComplexBarcodeEncoder implements BarcodeEncoderInterface{



    public function __construct(
        private TypeInterface $encoder,
        private RendererInterface $renderer,
        private BarcodeFormatterInterface $formatter,
    ) {

        // $this->encoder = new TypeCode128();
        // $this->renderer = $renderer;
    }
    
    /**
     * @inheritDoc
     */
    public function encode(BarcodeInterface $barcode, string $symbol, string|null $format = null, array $context = []): string {
        if(!$this->supports($barcode, $symbol, $format, $context)){
            throw new \InvalidArgumentException("Unsupported barcode or symbol");
        }
        
        $data = $this->formatter->format($barcode, $format, $context);
        $bCode = $this->encoder->getBarcode($data,);
        $result = $this->renderer->render($bCode, );
        return $result;
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