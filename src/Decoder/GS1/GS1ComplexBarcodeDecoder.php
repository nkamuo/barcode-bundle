<?php

namespace Nkamuo\Barcode\Decoder\GS1;

use Lamoda\GS1Parser\Parser\ParserInterface;
use Lamoda\GS1Parser\Validator\ValidatorInterface;
use Nkamuo\Barcode\Decoder\BarcodeDecoderInterface;
use Nkamuo\Barcode\Model\WritableBarcodeInterface;

class GS1ComplexBarcodeDecoder  implements BarcodeDecoderInterface
{

    public function __construct(
        private readonly ParserInterface $parser,
        private readonly ValidatorInterface $validator,
    ) {}

    /**
     * @inheritDoc
     */
    public function decode(WritableBarcodeInterface $barcode, string $data, string|null $symbol = null, string|null $format = null, array $context = []): void
    {

        $parsedData = $this->parser->parse($data);

        $ais = $parsedData->ais();
        // $raw = $parsedData->raw();

        foreach ($ais as $ai => $value) {
            $barcode
                ->addAttribute($ai, $value)
                    ;
        }

        $symbol = $parsedData->type();

        $barcode
            ->addMetadata('raw', $data)
            ->addMetadata('ais', $ais)
            ;
              
        if($symbol !== 'unknown'){
            $barcode->setSymbol($symbol)
                ->addMetadata('symbol', $symbol);
        }

        $barcode
        ->setStandard('GS1')
        ->addMetadata('standard',  'GS1')
        ->addMetadata('format', $format);
    }


    /**
     * @inheritDoc
     */
    public function getSupportedFormats(): array
    {
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
    public function getSupportedStandards(): array
    {
        return [
            'GS1',
            // 'ISO/IEC 15420',
            // 'ISO/IEC 15424',
            // 'ISO/IEC 15434',
            // 'ISO/IEC 16022',
            // 'ISO/IEC 18004',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getSupportedSymbols(): array
    {
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
    public function supports(string $data, string|null $symbol = null, string|null $format = null, array $context = []): bool
    {
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

        $resolution = $this->validator->validate($data);
        return $resolution->isValid();
    }
}
