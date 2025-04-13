<?php

namespace Nkamuo\Barcode\DependencyInjection\Collector;

use Nkamuo\Barcode\Formatter\BarcodeFormatterInterface;

class FormatterCollector
{
    /**
     * @var BarcodeFormatterInterface[]
     */
    private array $formatters = [];

    /**
     * Add a formatter to the collection.
     */
    public function addFormatter(BarcodeFormatterInterface $formatter): void
    {
        $this->formatters[] = $formatter;
    }

    /**
     * Retrieve all registered formatters.
     * @return BarcodeFormatterInterface[]
     */
    public function getFormatters(): array
    {
        return $this->formatters;
    }
}