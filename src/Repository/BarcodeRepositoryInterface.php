<?php

namespace Nkamuo\Barcode;

use Nkamuo\Barcode\Model\BarcodeInterface;

/**
 * Interface for a Barcode repository.
 *
 * This interface defines the methods for managing barcode instances,
 * including creating, updating, and deleting barcodes.
 */

interface BarcodeRepositoryInterface{
    /**
     * Creates a new barcode instance.
     *
     * @param BarcodeInterface $barcode The barcode instance to create.
     * @return void
     */
    public function create(BarcodeInterface $barcode): void;

    /**
     * Updates an existing barcode instance.
     *
     * @param BarcodeInterface $barcode The barcode instance to update.
     * @return void
     */
    public function update(BarcodeInterface $barcode): void;

    /**
     * Deletes a barcode instance.
     *
     * @param string $barcodeId The ID of the barcode to delete.
     * @return void
     */
    public function delete(string $barcodeId): void;
    /**
     * Finds a barcode instance by its ID.
     *
     * @param string $barcodeId The ID of the barcode to find.
     * @return BarcodeInterface|null The found barcode instance or null if not found.
     */
    public function find(string $barcodeId): ?BarcodeInterface;
    /**
     * Finds all barcode instances.
     *
     * @return array<int,BarcodeInterface> An array of all barcode instances.
     */
    public function findAll(): array;
    /**
     * Finds barcodes by a specific attribute.
     *
     * @param string $attributeName The name of the attribute to search by.
     * @param mixed $value The value of the attribute to search for.
     * @return array<int,BarcodeInterface> An array of matching barcode instances.
     */
    public function findByAttribute(string $attributeName, mixed $value): array;
}