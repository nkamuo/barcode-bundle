<?php

namespace Nkamuo\Barcode\Storage;

class ScopedHashStorage implements HashStorageInterface
{
    /**
     * @var HashStorageInterface The underlying storage implementation.
     */
    private HashStorageInterface $storage;

    /**
     * @var string The prefix to namespace all keys.
     */
    private string $prefix;

    /**
     * Constructor.
     *
     * @param HashStorageInterface $storage The underlying storage to wrap.
     * @param string $prefix The prefix to apply to keys.
     */
    public function __construct(HashStorageInterface $storage, string $prefix)
    {
        $this->storage = $storage;
        $this->prefix = $prefix;
    }

    public function get(string $key): mixed
    {
        return $this->storage->get($this->scopedKey($key));
    }

    public function set(string $key, mixed $value): void
    {
        $this->storage->set($this->scopedKey($key), $value);
    }

    public function delete(string $key): bool
    {
        return $this->storage->delete($this->scopedKey($key));
    }

    public function has(string $key): bool
    {
        return $this->storage->has($this->scopedKey($key));
    }

    public function all(): array
    {
        $allData = $this->storage->all();
        $scopedData = [];

        // Only return entries that start with the namespace prefix
        foreach ($allData as $fullKey => $value) {
            if (str_starts_with($fullKey, $this->prefix . '_')) {
                $scopedKey = substr($fullKey, strlen($this->prefix) + 1);
                $scopedData[$scopedKey] = $value;
            }
        }

        return $scopedData;
    }

    public function clear(): void
    {
        $allData = $this->storage->all();

        // Remove only entries that belong to this namespace.
        foreach ($allData as $fullKey => $value) {
            if (str_starts_with($fullKey, $this->prefix . '_')) {
                $this->storage->delete($fullKey);
            }
        }
    }

    /**
     * Apply the namespace prefix to a key.
     *
     * @param string $key The original key.
     * @return string The scoped key with prefix applied.
     */
    private function scopedKey(string $key): string
    {
        return "{$this->prefix}_{$key}";
    }
}