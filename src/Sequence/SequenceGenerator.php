<?php

namespace Nkamuo\Barcode\Generator\Sequence;

use Nkamuo\Barcode\Generator\Sequence\SequenceGeneratorInterface;
use Nkamuo\Barcode\Storage\HashStorageInterface;

class SequenceGenerator implements SequenceGeneratorInterface
{


    public function __construct(
        private readonly HashStorageInterface $storage,
    ){

    }

    /**
     * @inheritDoc
     */
    public function next(array $context = []): string
    {
        $key = $this->resolveSequenceKey($context);
        $current = $this->storage->get($key);
        $current = (int) $current;
        $this->storage->set($key, $current + 1);
        return $current;
    }

    /**
     * @inheritDoc
     */
    public function supports(array $context): bool
    {
        return array_key_exists($this->resolveSequenceKey($context), $context);
    }



    protected function resolveSequenceKey(array $context = []): string{
        return $context['prefix'] ?? 'sequence_number';
    }
}