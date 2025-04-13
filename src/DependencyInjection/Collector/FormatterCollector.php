<?php

namespace Nkamuo\Barcode\Service;

use Nkamuo\Barcode\Formatter\FormatterInterface;

class FormatterCollector
{
    /**
     * @var FormatterInterface[]
     */
    private array $formatters = [];

    /**
     * Add a formatter to the collection.
     */
    public function addFormatter(FormatterInterface $formatter): void
    {
        $this->formatters[] = $formatter;
    }

    /**
     * Retrieve all registered formatters.
     */
    public function getFormatters(): array
    {
        return $this->formatters;
    }
}