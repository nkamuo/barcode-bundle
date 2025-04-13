<?php
namespace Nkamuo\Barcode\Encoder\GS1;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\ConsoleWriter;
use Endroid\QrCode\Writer\WriterInterface;
use InvalidArgumentException;
use Nkamuo\Barcode\Encoder\BarcodeEncoderInterface;
use Nkamuo\Barcode\Formatter\BarcodeFormatterInterface;
use Nkamuo\Barcode\Model\BarcodeInterface;

class GS1GRCodeEncoder implements BarcodeEncoderInterface{


    public function __construct(
        private readonly WriterInterface           $writer,
        private readonly BarcodeFormatterInterface $formatter,
        ) {
    }
    
    /**
     * @inheritDoc
     */
    public function encode(BarcodeInterface $barcode, string $symbol, string|null $format = null, array $context = []): string {
        if(!$this->supports($barcode, $symbol, $format, $context)){
            throw new InvalidArgumentException("Unsupported barcode or symbol");
        }
        
        $data = $this->formatter->format($barcode, $format, $context);
        $qrcode = new QrCode($data);
        $result = $this->writer->write($qrcode, options: []);
        if($this->writer instanceof ConsoleWriter){
            // If the writer is ConsoleWriter, we need to render it to a string
            return $result->getString();
        }
        return $result->getDataUri();
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
        $supportedSymbols = ['QR',];
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