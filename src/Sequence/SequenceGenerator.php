<?php

namespace Nkamuo\Barcode\Sequence;

use Nkamuo\Barcode\Storage\HashStorageInterface;

class SequenceGenerator implements SequenceGeneratorInterface
{


    public function __construct(
        private readonly HashStorageInterface $storage,
        private readonly array $config = [],
    ){

    }



    /**
     * @inheritDoc
     */
    public function supports(array $context): bool
    {
        return array_key_exists($this->resolveSequenceKey($context), $context);
    }


    /**
     * @inheritDoc
     */
    public function current(array $context = []): string|int
    {
        $key = $this->resolveSequenceKey($context);
        return $this->storage->get($key);
    }


    /**
     * @inheritDoc
     */
    public function peekNext(array $context = []): string|int
    {
        $key = $this->resolveSequenceKey($context);
        $current = $this->storage->get($key);
        return $this->resolveNext($current, $context);
    }


    /**
     * @inheritDoc
     */
    public function next(array $context = [], int|string|null $expected = null): string|int
    {
        $key = $this->resolveSequenceKey($context);
        $current = $this->storage->get($key);
        $next = $this->resolveNext($current, $context);
        $this->storage->set($key, $next);
        return $next;
    }




    protected function resolveSequenceKey(array $context = []): string{
        return $context['prefix'] ?? 'sequence_number';
    }

    protected function resolveNext(string|int $current = null, array $context = []): string|int
    {
        if($current !== null) {
            $_current = (int)$current;
            return $_current + 1;
        }{
            $start = ($this->config['start_at'] ?? 1);
            return (int)$start;
    }
    }

}