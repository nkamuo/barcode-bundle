<?php

namespace Nkamuo\Barcode\Generator\Storage;

interface PersistentStorageInterface
{
    /**
     * Get a value by its key.
     *
     * @param string $key
     * @return mixed|null Null if the key does not exist.
     */
    public function get(string $key): mixed;

    /**
     * Save or update a value by its key.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void;

    /**
     * Delete a key-value pair.
     *
     * @param string $key
     * @return bool True if the key was successfully deleted, false otherwise.
     */
    public function delete(string $key): bool;

    /**
     * Check if a key exists.
     *
     * @param string $key
     * @return bool True if the key exists, false otherwise.
     */
    public function exists(string $key): bool;

    /**
     * Clear all key-value pairs.
     *
     * @return void
     */
    public function clear(): void;
}