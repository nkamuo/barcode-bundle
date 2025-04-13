<?php

namespace Nkamuo\Barcode\Generator\Sequence;

interface SequenceGeneratorInterface
{

    /**
     * @param array $context
     * @return string|int
     */
    public function current(array $context = []): string|int;

    /**
     * Generates the next sequence number based on the provided context.
     * @param array $context
     * @return string|int
     */
    public function next(array $context = []): string|int;


    public function reset(array $context = []): void;



    /**
     * Checks if this generator supports the given context.
     * @param array $context
     * @return bool
     */
    public function supports(array $context): bool;
}