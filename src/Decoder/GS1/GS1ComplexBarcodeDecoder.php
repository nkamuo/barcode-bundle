<?php

namespace Nkamuo\Barcode\Decoder\GS1;

use Lamoda\GS1Parser\Constants;
use Lamoda\GS1Parser\Parser\Parser;
use Lamoda\GS1Parser\Parser\ParserConfig;
use Lamoda\GS1Parser\Parser\ParserInterface;
use Lamoda\GS1Parser\Validator\Validator;
use Lamoda\GS1Parser\Validator\ValidatorConfig;
use Lamoda\GS1Parser\Validator\ValidatorInterface;
use Nkamuo\Barcode\Decoder\BarcodeDecoderInterface;
use Nkamuo\Barcode\Formatter\GS1\DataBarcodeFormatter;
use Nkamuo\Barcode\Model\BarcodeInterface;
use Nkamuo\Barcode\Model\WritableBarcodeInterface;

class GS1ComplexBarcodeDecoder  implements BarcodeDecoderInterface
{


    public const KNOWN_AIS = [
        '01' => 'Global Trade Item Number (GTIN)',
        '02' => 'Product Number',
        '10' => 'Batch or Lot Number',
        '11' => 'Production Date',
        '12' => 'Due Date',
        '13' => 'Packaging Date',
        '15' => 'Best Before Date',
        '17' => 'Expiration Date',
        '20' => 'Item Reference',
        // Add more AIs as needed
        '21' => 'Serial Number',
        '22' => 'Consumer Product Code',
        '30' => 'Price',
        '37' => 'Count of Items',
        '400' => 'Country of Origin',
        '410' => 'Shipping Container Code',
        // Assets - Returnable and other types
    ];

    public const DEFAULT_FUNC_PREFIX_MAP = DataBarcodeFormatter::DEFAULT_FUNC_PREFIX_MAP;


    private readonly ParserInterface $parser;
    private readonly ValidatorInterface $validator;

    public function __construct(
        ?ParserInterface $parser = null,
        ?ValidatorInterface $validator = null,
    ) {
        $this->parser = $parser ?? new Parser($this->getParserConfig());
        $this->validator = $validator ?? new Validator($this->parser, $this->getValidatorConfig());
    }

    /**
     * @inheritDoc
     */
    public function decode(WritableBarcodeInterface $barcode, string $data, string|null $symbol = null, string|null $format = null, array $context = []): BarcodeInterface
    {

        $parsedData = $this->parser->parse($data);

        $ais = $parsedData->ais();
        // $raw = $parsedData->raw();

        foreach ($ais as $ai => $value) {
            $barcode
                ->addAttribute($ai, $value);
        }

        $symbol = $parsedData->type();

        $barcode
            ->setValue($data)
            ->addMetadata('raw', $data)
            ->addMetadata('ais', $ais)
        ;

        if ($symbol !== 'unknown') {
            $barcode
                ->setSymbol($symbol)
                ->addMetadata('symbol', $symbol);
        }

        return $barcode
            ->setStandard('GS1')
            ->addMetadata('standard',  'GS1')
            ->addMetadata('format', $format);
    }


    /**
     * @inheritDoc
     */
    public function supports(string $data, string|null $symbol = null, string|null $format = null, array $context = []): bool
    {
        // Check if the standard is supported
        if (($context['standard'] ?? null) !== null && !in_array($context['standard'], $this->getSupportedStandards())) {
            return false;
        }

        foreach (self::DEFAULT_FUNC_PREFIX_MAP as $key => $value) {
            if (str_starts_with($data, $value)) {
                return true;
            }
        }
        return false;

        // $resolution = $this->validator->validate($data);
        // return $resolution->isValid();
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



    protected function getValidatorConfig(array $config = []): ValidatorConfig
    {
        return (new ValidatorConfig())
            ;
    }

    protected function getParserConfig(array $config = []): ParserConfig{
        return (new ParserConfig())
            // ->setFnc1PrefixMap(self::DEFAULT_FUNC_PREFIX_MAP)
            ->setKnownAIs($this->getKnownAIs());
    }

    protected function getKnownAIs(array $config = []): array
    {
        return array_map('strval', array_keys(self::KNOWN_AIS));
    }
}
