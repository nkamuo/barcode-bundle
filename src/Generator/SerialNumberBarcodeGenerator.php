<?php

namespace Nkamuo\Barcode\Generator;

use Nkamuo\Barcode\Generator\BarcodeGeneratorInterface;
use Nkamuo\Barcode\Model\BarcodeInterface;
use Nkamuo\Barcode\Model\WritableBarcodeInterface;
use Nkamuo\Barcode\Sequence\Exception\UnexpectedSequenceEntryException;
use Nkamuo\Barcode\Sequence\SequenceGeneratorInterface;

class SerialNumberBarcodeGenerator implements BarcodeGeneratorInterface
{

    public const DEFAULT_PREFIX = '';

    public const PAD_CONFIG_KEY = 'pad_length';

    public const PREFIX_CONFIG_KEY = 'prefix';

    public const TYPE_CONFIG_KEY = 'type';

    public const SEQUENCE_KEY = 'sequence_number';

    public const DEFAULT_PAD_LENGTH = 0;

    public function __construct(
        private readonly SequenceGeneratorInterface $sequenceGenerator,
        private readonly array $config = [],
    ){}

    /**
     * @inheritDoc
     * @throws UnexpectedSequenceEntryException
     */
    public function generate(WritableBarcodeInterface $barcode, array $context = []): BarcodeInterface
    {
        $padLength = $context[self::PAD_CONFIG_KEY] ?? $this->config[self::PAD_CONFIG_KEY] ?? self::DEFAULT_PAD_LENGTH;
        $prefix = $this->resolvePrefix($context);

        $serialNumber = $this->sequenceGenerator->peekNext($context);
        $serialNumber = str_pad($serialNumber, $padLength, '0', STR_PAD_LEFT);
        $data = sprintf('%s%s', $prefix, $serialNumber);

        $this->sequenceGenerator->next($context, expected: $serialNumber);

        return $barcode
                ->setValue($data)

            ;
    }

    /**
     * @inheritDoc
     */
    public function supports(array $context): bool
    {
        return ($context[self::TYPE_CONFIG_KEY] ?? null) === self::SEQUENCE_KEY;
    }


    protected function resolvePrefix(array $context = []): string{
        return $context[self::PREFIX_CONFIG_KEY] ?? $this->config[self::PREFIX_CONFIG_KEY] ?? self::DEFAULT_PREFIX;
    }
}