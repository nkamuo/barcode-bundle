<?php

namespace Nkamuo\Barcode\Storage;

class InMemoryHashStorage implements HashStorageInterface
{
    /**
     * @var array<string, mixed> The in-memory key-value store.
     */
    private array $storage = [];

    public function get(string $key): mixed
    {
        return $this->storage[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $this->storage[$key] = $value;
    }

    public function delete(string $key): bool
    {
        if (isset($this->storage[$key])) {
            unset($this->storage[$key]);
            return true;
        }

        return false;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->storage);
    }

    public function all(): array
    {
        return $this->storage;
    }

    public function clear(): void
    {
        $this->storage = [];
    }
}