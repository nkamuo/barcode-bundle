#### Barcode Repository

The `BarcodeRepository` is responsible for managing the persistence and retrieval of `BarcodeInterface` instances. It provides methods for saving, deleting, finding, and searching barcodes based on their attributes and metadata. The repository serves as a bridge between the barcode model and the underlying storage mechanism, whether in-memory, database, or other storage systems.

---

### Purpose of the Repository

The repository abstracts the storage layer, allowing you to manage barcodes without worrying about the underlying storage implementation. This makes it easy to switch between different storage backends (e.g., in-memory, database) without changing the business logic.

---

### Example: Using the InMemoryBarcodeRepository

The `InMemoryBarcodeRepository` is a simple implementation of the `BarcodeRepositoryInterface` that stores barcodes in memory.

```php
use Nkamuo\Barcode\Repository\InMemoryBarcodeRepository;
use Nkamuo\Barcode\Model\Barcode;

$repository = new InMemoryBarcodeRepository();

// Create a new barcode
$barcode = new Barcode(value: '0123456789012', type: 'GTIN');
$repository->save($barcode);

// Find a barcode by its value
$foundBarcode = $repository->findById('0123456789012');
if ($foundBarcode) {
    echo $foundBarcode->getValue(); // Outputs: 0123456789012
}

// Search for barcodes with specific attributes
$results = $repository->search($barcode);
foreach ($results as $result) {
    echo $result->getValue();
}

// Delete a barcode
$repository->delete($barcode);
```

---

### Repository Interface

The `BarcodeRepositoryInterface` defines the contract for any repository implementation. This ensures that custom repositories can be created while adhering to the same interface.

```php
interface BarcodeRepositoryInterface {
    public function save(BarcodeInterface $barcode, array $context = []): void;
    public function delete(BarcodeInterface $barcode, array $context = []): void;
    public function find(BarcodeInterface $barcode, array $context = []): ?BarcodeInterface;
    public function search(BarcodeInterface $barcode, array $context = []): array;
    public function findById(string $barcodeId, array $context = []): ?BarcodeInterface;
}
```

---

### Customizing the Repository

You can implement your own repository by extending the `BarcodeRepositoryInterface`. For example, here is how you can create a database-backed repository using Doctrine DBAL.

---

### Example: Implementing a DBALBarcodeRepository

The `DBALBarcodeRepository` uses Doctrine DBAL to store and retrieve barcodes from a database.

```php
use Nkamuo\Barcode\Repository\BarcodeRepositoryInterface;
use Nkamuo\Barcode\Model\BarcodeInterface;
use Doctrine\DBAL\Connection;

class DBALBarcodeRepository implements BarcodeRepositoryInterface {
    private Connection $connection;
    private string $tableName;

    public function __construct(Connection $connection, string $tableName = 'barcodes') {
        $this->connection = $connection;
        $this->tableName = $tableName;
    }

    public function save(BarcodeInterface $barcode, array $context = []): void {
        $this->connection->insert($this->tableName, [
            'id' => $barcode->getValue(),
            'type' => $barcode->getType(),
            'standard' => $barcode->getStandard(),
            'metadata' => json_encode($barcode->getMetadata()),
        ]);
    }

    public function delete(BarcodeInterface $barcode, array $context = []): void {
        $this->connection->delete($this->tableName, ['id' => $barcode->getValue()]);
    }

    public function find(BarcodeInterface $barcode, array $context = []): ?BarcodeInterface {
        $query = $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->tableName)
            ->where('id = :id')
            ->setParameter('id', $barcode->getValue())
            ->executeQuery();

        $result = $query->fetchAssociative();
        if (!$result) {
            return null;
        }

        return new Barcode(
            value: $result['id'],
            type: $result['type'],
            standard: $result['standard'],
            metadata: json_decode($result['metadata'], true)
        );
    }

    public function search(BarcodeInterface $barcode, array $context = []): array {
        $query = $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->tableName)
            ->where('type = :type')
            ->setParameter('type', $barcode->getType())
            ->executeQuery();

        $results = [];
        foreach ($query->fetchAllAssociative() as $row) {
            $results[] = new Barcode(
                value: $row['id'],
                type: $row['type'],
                standard: $row['standard'],
                metadata: json_decode($row['metadata'], true)
            );
        }

        return $results;
    }

    public function findById(string $barcodeId, array $context = []): ?BarcodeInterface {
        $query = $this->connection->createQueryBuilder()
            ->select('*')
            ->from($this->tableName)
            ->where('id = :id')
            ->setParameter('id', $barcodeId)
            ->executeQuery();

        $result = $query->fetchAssociative();
        if (!$result) {
            return null;
        }

        return new Barcode(
            value: $result['id'],
            type: $result['type'],
            standard: $result['standard'],
            metadata: json_decode($result['metadata'], true)
        );
    }
}
```

---

### Relation to Other Components

The repository interacts with other components in the following ways:

1. **Factory**:
   - The factory can create `BarcodeInterface` instances that are saved or retrieved by the repository.

2. **Processors**:
   - Processors can use the repository to persist barcodes after processing or retrieve barcodes for further operations.

3. **Generators**:
   - Generators like `SerialNumberBarcodeGenerator` can use the repository to ensure uniqueness of generated barcodes.

4. **Decoders**:
   - Decoders can use the repository to check if a decoded barcode already exists in the system.

---

### Error Handling

The repository implementations should handle storage-specific exceptions (e.g., database connection errors) and rethrow them as domain-specific exceptions if necessary.

---

This documentation provides an overview of how to use, extend, and implement barcode repositories in your application. For more advanced use cases, refer to the specific repository implementations in the source code.