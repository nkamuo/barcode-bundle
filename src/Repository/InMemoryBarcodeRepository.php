<?php

namespace Nkamuo\Barcode\Repository;

use Nkamuo\Barcode\Model\BarcodeInterface;

class InMemoryBarcodeRepository implements BarcodeRepositoryInterface
{
    /**
     * @var BarcodeInterface[] In-memory storage for barcodes, keyed by their IDs.
     */
    private array $storage = [];

    /**
     * Creates a new barcode or saves an existing one.
     *
     * @param BarcodeInterface $barcode The barcode to save.
     * @param array $context Additional context (not used for in-memory storage).
     * @return void
     */
    public function save(BarcodeInterface $barcode, array $context = []): void
    {
        $this->storage[$this->resolveId($barcode)] = $barcode;
    }

    /**
     * Deletes a barcode instance.
     *
     * @param BarcodeInterface $barcode The barcode to delete.
     * @param array $context Additional context (not used for in-memory storage).
     * @return void
     */
    public function delete(BarcodeInterface $barcode, array $context = []): void
    {
        unset($this->storage[$this->resolveId($barcode)]);
    }

    /**
     * Finds a barcode by attributes and metadata.
     *
     * @param BarcodeInterface $barcode The barcode to find.
     * @param array $context Additional context (not used for in-memory implementation).
     * @return BarcodeInterface|null The found barcode instance or null if none matches.
     */
    public function find(BarcodeInterface $barcode, array $context = []): ?BarcodeInterface
    {
        foreach ($this->storage as $storedBarcode) {
            if ($this->matches($barcode, $storedBarcode)) {
                return $storedBarcode;
            }
        }

        return null;
    }

    /**
     * Searches for barcodes based on attributes or metadata.
     *
     * @param BarcodeInterface $barcode A barcode instance carrying search criteria.
     * @param array $context Additional context (not used for in-memory implementation).
     * @return BarcodeInterface[] A list of barcodes that match the search criteria.
     */
    public function search(BarcodeInterface $barcode, array $context = []): array
    {
        $results = [];

        foreach ($this->storage as $storedBarcode) {
            if ($this->matches($barcode, $storedBarcode)) {
                $results[] = $storedBarcode;
            }
        }

        return $results;
    }

    /**
     * Finds a barcode by its ID.
     *
     * @param string $barcodeId The unique ID of the barcode.
     * @param array $context Additional context (not used for in-memory implementation).
     * @return BarcodeInterface|null The found barcode instance or null if not found.
     */
    public function findById(string $barcodeId, array $context = []): ?BarcodeInterface
    {
        return $this->storage[$barcodeId] ?? null;
    }

    /**
     * Checks if two barcodes match based on their attributes and metadata.
     *
     * @param BarcodeInterface $barcode1 The first barcode.
     * @param BarcodeInterface $barcode2 The second barcode to compare against.
     * @return bool True if the barcodes match, false otherwise.
     */
    private function matches(BarcodeInterface $barcode1, BarcodeInterface $barcode2): bool
    {
        // Replace this with actual attribute comparison logic.
        return $this->resolveId($barcode1) === $this->resolveId($barcode2);
    }


    public function resolveId(BarcodeInterface $barcode): string
    {
        return $barcode->getValue();
    }
}