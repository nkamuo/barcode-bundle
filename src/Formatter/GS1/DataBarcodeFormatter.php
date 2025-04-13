<?php

namespace Nkamuo\Barcode\Formatter\GS1;

use Lamoda\GS1Parser\Barcode;
use Lamoda\GS1Parser\Constants;
use Nkamuo\Barcode\Formatter\BarcodeFormatterInterface;
use Nkamuo\Barcode\Model\BarcodeInterface;

class DataBarcodeFormatter implements BarcodeFormatterInterface
{

    public const DEFAULT_FUNC_PREFIX_MAP = [
        Barcode::TYPE_GS1_DATAMATRIX => Constants::FNC1_GS1_DATAMATRIX_SEQUENCE,
        Barcode::TYPE_GS1_128        => Constants::FNC1_GS1_128_SEQUENCE,
        Barcode::TYPE_GS1_QRCODE     => Constants::FNC1_GS1_QRCODE_SEQUENCE,
        Barcode::TYPE_EAN            => Constants::FNC1_GS1_EAN_SEQUENCE,
    ];

    public const DEFAULT_ENCODABLE_VALUE_CHARACTERS_SET = [
        ' ',
        '!',
        '"',
        '%',
        '&',
        '\'',
        '(',
        ')',
        '*',
        '+',
        ',',
        '-',
        '.',
        '/',
        '0',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        ':',
        ';',
        '<',
        '=',
        '>',
        '?',
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        'a',
        'b',
        'c',
        'd',
        'e',
        'f',
        'g',
        'h',
        'i',
        'j',
        'k',
        'l',
        'm',
        'n',
        'o',
        'p',
        'q',
        'r',
        's',
        't',
        'u',
        'v',
        'w',
        'x',
        'y',
        'z',
    ];

    private array $fnc1PrefixMap = self::DEFAULT_FUNC_PREFIX_MAP;

    private array $encodableValueCharactersSet = self::DEFAULT_ENCODABLE_VALUE_CHARACTERS_SET;

    private string $groupSeparator = Constants::GROUP_SEPARATOR_SYMBOL;

    private string $defaultSymbol = Barcode::TYPE_GS1_128;

    private array $fixedLengthAIs = [];

    public function __construct(
        private readonly array $config = [],
    ) {
        $this->encodableValueCharactersSet = $this->config['encodableValueCharactersSet'] ?? self::DEFAULT_ENCODABLE_VALUE_CHARACTERS_SET;
        $this->fnc1PrefixMap = $this->config['fnc1PrefixMap'] ?? self::DEFAULT_FUNC_PREFIX_MAP;
        $this->groupSeparator = $this->config['groupSeparator'] ?? Constants::GROUP_SEPARATOR_SYMBOL;
        $this->defaultSymbol = $this->config['defaultSymbol'] ?? Barcode::TYPE_GS1_128;
        $this->fixedLengthAIs = $this->buildFixedLengthAIs($config);
    }
    /**
     * @inheritDoc
     */
    public function format(BarcodeInterface $barcode, ?string $format = null, array $context = []): string
    {
        $symbol = $context['symbol'] ?? $barcode->getSymbol() ?? $this->defaultSymbol;
        $fnc1Prefix = $this->fnc1PrefixMap[$symbol] ?? null;

        if ($fnc1Prefix === null) {
            throw new \InvalidArgumentException("Unsupported symbol: $symbol");
        }

        $ais = $barcode->getAttributes();
        $formattedData = $fnc1Prefix;
        $total = count($ais);

        foreach ($ais as $index => $ai) {
            $code = $ai->getCode();
            $value = $ai->getValue();

            // Optional: Validate characters
            foreach (str_split($value) as $char) {
                if (!in_array($char, $this->encodableValueCharactersSet, true)) {
                    throw new \InvalidArgumentException("Invalid character '$char' in value of AI $code");
                }
            }

            // Handle fixed-length AIs
            if (array_key_exists($code, $this->fixedLengthAIs)) {
                $length = $this->fixedLengthAIs[$code];
                $value = substr($value, 0, $length); // truncate if too long
                $formattedData .= sprintf('%s%s', $code, $value);
            } else {
                // Variable-length AIs
                $formattedData .= sprintf('%s%s', $code, $value);

                // Add group separator unless it's the last element
                if ($index < $total - 1) {
                    $formattedData .= $this->groupSeparator;
                }
            }
        }

        return $formattedData;
    }


    /**
     * @inheritDoc
     */
    public function supports(BarcodeInterface $barcode, ?string $format = null, array $context = []): bool
    {
        if (!($barcode->getType() === 'Code128' && $barcode->getStandard() === 'GS1')) {
            return false;
        }
        $symbol = $context['symbol'] ?? $barcode->getSymbol() ?? $this->defaultSymbol;
        $fnc1Prefix = $this->fnc1PrefixMap[$symbol] ?? null;

        if ($fnc1Prefix === null) {
            return false;
        }
        return true;

        // You can also check the format and context if needed
    }


    private function buildFixedLengthAIs(array $config = []): array
    {
        return [

            '00' => 20,
            '01' => 16,
            '02' => 16,
            '03' => 16,
            '04' => 18,
            '11' => 8,
            '12' => 8,
            '13' => 8,
            '14' => 8,
            '15' => 8,
            '16' => 8,
            '17' => 8,
            '18' => 8,
            '19' => 8,
            '20' => 4,
            '41' => 16,
            // Variable-range based entries
        ] + self::expandRangeFixedLength('310', '316', 10)
            + self::expandRangeFixedLength('320', '326', 10)
            + self::expandRangeFixedLength('330', '336', 10)
            + self::expandRangeFixedLength('340', '346', 10)
            + self::expandRangeFixedLength('350', '356', 10)
            + self::expandRangeFixedLength('360', '366', 10);
    }

    private static function expandRangeFixedLength(string $start, string $end, int $length): array
    {
        $result = [];
        for ($i = (int) $start; $i <= (int) $end; $i++) {
            for ($j = 0; $j <= 9; $j++) {
                $result[$i . $j] = $length;
            }
        }
        return $result;
    }
}
