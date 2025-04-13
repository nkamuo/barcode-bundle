<?php

namespace Nkamuo\Barcode\Repository;

use Nkamuo\Barcode\Model\BarcodeInterface;

/**
 * Interface for a Barcode repository.
 *
 * This interface defines the methods for managing barcode instances,
 * including creating, updating, and deleting barcodes.
 */

interface BarcodeRepositoryInterface{
    /**
     * Creates a new barcode instance or save existing.
     *
     * @param BarcodeInterface $barcode The barcode instance to create.
     * @param array $context
     * @return void
     */
    public function save(BarcodeInterface $barcode, array $context = []): void;

    /**
     * Deletes a barcode instance.
     *
     * @param BarcodeInterface $barcode
     * @param array $context
     * @return void
     */
    public function delete(BarcodeInterface $barcode, array $context = []): void;

    /**
     * Used to perform a more involved search of the barcode using both attributes and metadata
     *
     * @param BarcodeInterface $barcode
     * @param array $context
     * @return BarcodeInterface|null The found barcode instance or null if not found.
     */
    public function find(BarcodeInterface $barcode, array $context = []): ?BarcodeInterface;

    /**
     * @param BarcodeInterface $barcode
     * @param array $context
     * @return array<int,BarcodeInterface>
     */
    public function search(BarcodeInterface $barcode, array $context = []): array;

    /**
     * Finds a barcode instance by its ID.
     *
     * @param string $barcodeId The ID of the barcode to find.
     * @return BarcodeInterface|null The found barcode instance or null if not found.
     */
    public function findById(string $barcodeId, array $context = []): ?BarcodeInterface;

}