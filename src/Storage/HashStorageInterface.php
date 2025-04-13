<?php

namespace Nkamuo\Barcode\Storage;

interface HashStorageInterface
{
    /**
     * Get a value by its key.
     *
     * @param string $key The key of the value to retrieve.
     * @return mixed|null Returns the value if it exists, or null otherwise.
     */
    public function get(string $key): mixed;

    /**
     * Save or update a value by its key.
     *
     * @param string $key The key to save the value under.
     * @param mixed $value The value to save.
     */
    public function set(string $key, mixed $value): void;

    /**
     * Delete a key-value pair.
     *
     * @param string $key The key of the value to delete.
     * @return bool Returns true if the key was successfully deleted, false otherwise.
     */
    public function delete(string $key): bool;

    /**
     * Check if a key exists.
     *
     * @param string $key The key to check for existence.
     * @return bool Returns true if the key exists, false otherwise.
     */
    public function has(string $key): bool;

    /**
     * Retrieve all key-value pairs.
     *
     * @return array All stored data as an associative array.
     */
    public function all(): array;

    /**
     * Clear all key-value pairs from storage.
     *
     * @return void
     */
    public function clear(): void;
}