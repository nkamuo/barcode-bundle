<?php
namespace Nkamuo\Barcode\Decoder\GS1;

use Nkamuo\Barcode\Decoder\BarcodeDecoderInterface;
use Nkamuo\Barcode\Model\WritableBarcodeInterface;

class GS1SimpleBarcodeDecoder  implements BarcodeDecoderInterface{


    public const TYPE_TO_AI_MAPS = [
        'GTIN' => '01',
        'SSCC' => '00',
        'GLN' => '414',
        'GRAI' => '8003',
        'GIAI' => '8004',
        'GSRN' => '8018',
        'GDTI' => '253',
        'GINC' => '401',
        'GSIN' => '402',
    ];

   
    public function decode(WritableBarcodeInterface $barcode, string $data, string|null $symbol = null, string|null $format = null, array $context = []): void{
        $type = GS1CodeValidator::detectAndValidate($data);
        if ($type === null) {
            throw new \InvalidArgumentException("Invalid GS1 barcode data");
        }

        $ai = self::TYPE_TO_AI_MAPS[$type] ?? null;
        if ($ai === null) {
            throw new \InvalidArgumentException("Unsupported GS1 barcode type: $type");
        }
        
        $barcode
            ->setType($type)
            ->setStandard('GS1')
            ->addMetadata('standard', 'GS1')
            ->addMetadata('format', $format)
            ->addMetadata('symbol', $symbol)
            // 
            ->addAttribute($ai, $data)
            ;
    }
   
    
    /**
     * @inheritDoc
     */
    public function getSupportedFormats(): array {
        return [
            'GS1-128',
            'GS1-Databar',
            'GS1-DataMatrix',
            'GS1-QR',
            'GS1-UPC',
            'GS1-EAN',
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function getSupportedStandards(): array {
        return [
            'GS1',
            'ISO/IEC 15420',
            'ISO/IEC 15424',
            'ISO/IEC 15434',
            'ISO/IEC 16022',
            'ISO/IEC 18004',
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function getSupportedSymbols(): array {
        return [
            'GS1-128',
            'GS1-Databar',
            'GS1-DataMatrix',
            'GS1-QR',
            'GS1-UPC',
            'GS1-EAN',
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function supports(string $data, string|null $symbol = null, string|null $format = null, array $context = []): bool {
        // Check if the barcode is a GS1 barcode
        if ($format !== null && !in_array($format, $this->getSupportedFormats())) {
            return false;
        }

        // Check if the symbol is supported
        if ($symbol !== null && !in_array($symbol, $this->getSupportedSymbols())) {
            return false;
        }

        // Check if the standard is supported
        if ($context['standard'] !== null && !in_array($context['standard'], $this->getSupportedStandards())) {
            return false;
        }

        $type = GS1CodeValidator::detectAndValidate($data);
        return $type !== null;
    }
}