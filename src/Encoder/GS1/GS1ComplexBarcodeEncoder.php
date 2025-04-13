<?php
namespace Nkamuo\Barcode\Encoder\GS1;

use Nkamuo\Barcode\Encoder\BarcodeEncoderInterface;
use Nkamuo\Barcode\Formatter\BarcodeFormatter;
use Nkamuo\Barcode\Formatter\BarcodeFormatterInterface;
use Nkamuo\Barcode\Model\BarcodeInterface;
use Picqer\Barcode\Renderers\PngRenderer;
use Picqer\Barcode\Renderers\RendererInterface;
use Picqer\Barcode\Types\TypeCode128;
use Picqer\Barcode\Types\TypeInterface;

class GS1ComplexBarcodeEncoder implements BarcodeEncoderInterface{

    private readonly TypeInterface             $encoder;
        private readonly RendererInterface         $renderer;
        private readonly BarcodeFormatterInterface $formatter;


    public function __construct(
        ?BarcodeFormatterInterface $formatter = null,
        ?TypeInterface             $encoder = null,
        ?RendererInterface         $renderer = null,
    ) {

        $this->formatter = $formatter ?? new BarcodeFormatter();
         $this->encoder = $encoder ?? new TypeCode128();
         $this->renderer = $renderer ?? new PngRenderer();
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
        return $this->renderer->render($bCode, );
    }
    
    /**
     * @inheritDoc
     */
    public function supports(BarcodeInterface $barcode, string $symbol, string|null $format = null, array $context = []): bool {
        
        // return true;
        // Check if the barcode is a GS1 barcode
        if ( ($standard = $barcode->getStandard()) && strtoupper($standard) !== 'GS1') {
            return false;
        }

        // Check if the symbol is supported
        $supportedSymbols = ['QR', 'EAN-13', 'UPC', 'CODE128'];
        if (!in_array(strtoupper($symbol), $supportedSymbols)) {
            return false;
        }

        // Check if the format is supported
        $supportedFormats = ['PNG', 'SVG', 'PDF'];
        if ($format !== null && !in_array(strtoupper($format), $supportedFormats)) {
            return false;
        }

        return true;
    }
}