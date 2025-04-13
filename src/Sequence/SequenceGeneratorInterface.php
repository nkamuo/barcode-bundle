<?php

namespace Nkamuo\Barcode\Sequence;

use Nkamuo\Barcode\Sequence\Exception\UnexpectedSequenceEntryException;

interface SequenceGeneratorInterface
{

    /**
     * @param array $context
     * @return string|int
     */
    public function current(array $context = []): string|int;

    /**
     * Generates the next sequence number based on the provided context without saving it.
     * @param array $context
     * @return string|int
     */
    public function peekNext(array $context = []): string|int;

    /**
     * Generates the next sequence number based on the provided context.
     * @param array $context
     * @param string|int|null $expected
     * @return string|int
     * @throws UnexpectedSequenceEntryException when $expected is provided and does not match the next value
     */
    public function next(array $context = [], string|int|null $expected = null): string|int;


    /**
     * Checks if this generator supports the given context.
     * @param array $context
     * @return bool
     */
    public function supports(array $context): bool;
}