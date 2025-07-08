<?php

namespace Nkamuo\Barcode\Storage;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\Schema;

class DBALHashStorage implements HashStorageInterface
{
    private Connection $connection;
    private string $tableName;
    private bool $autoCreateTable;

    public function __construct(Connection $connection, array $config = [], string $tableName = 'barcode_hash_table', bool $autoCreateTable = true)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->autoCreateTable = $autoCreateTable;
    }

    public function get(string $key): mixed
    {
        $this->ensureTableExists();
        $platform = $this->connection->getDatabasePlatform();
        $sql = sprintf('SELECT %s FROM %s WHERE %s = ?', 
            $platform->quoteIdentifier('value'),
            $platform->quoteIdentifier($this->tableName),
            $platform->quoteIdentifier('key')
        );
        $result = $this->connection->fetchOne($sql, [$key]);
        return $result !== false ? json_decode($result, associative: true) : null;
    }

    public function set(string $key, mixed $value): void
    {
        $this->ensureTableExists();
        $platform = $this->connection->getDatabasePlatform();
        $table = $platform->quoteIdentifier($this->tableName);
        $keyCol = $platform->quoteIdentifier('key');
        $valueCol = $platform->quoteIdentifier('value');
        
        // Use upsert/merge logic for cross-platform support
        if (($platform instanceof PostgreSQLPlatform)) {
            $sql = sprintf(
                'INSERT INTO %s (%s, %s) VALUES (?, ?) ON CONFLICT (%s) DO UPDATE SET %s = EXCLUDED.%s',
                $table, $keyCol, $valueCol, $keyCol, $valueCol, $valueCol
            );
        } else {
            // MySQL and SQLite support REPLACE INTO
            $sql = sprintf(
                'REPLACE INTO %s (%s, %s) VALUES (?, ?)',
                $table, $keyCol, $valueCol
            );
        }
        $this->connection->executeStatement($sql, [$key, json_encode($value)]);
    }

    public function delete(string $key): bool
    {
        $this->ensureTableExists();
        $platform = $this->connection->getDatabasePlatform();
        $sql = sprintf('DELETE FROM %s WHERE %s = ?', 
            $platform->quoteIdentifier($this->tableName),
            $platform->quoteIdentifier('key')
        );
        $count = $this->connection->executeStatement($sql, [$key]);
        return $count > 0;
    }

    public function has(string $key): bool
    {
        $this->ensureTableExists();
        $platform = $this->connection->getDatabasePlatform();
        $sql = sprintf('SELECT 1 FROM %s WHERE %s = ?', 
            $platform->quoteIdentifier($this->tableName),
            $platform->quoteIdentifier('key')
        );
        $result = $this->connection->fetchOne($sql, [$key]);
        return $result !== false;
    }

    public function all(): array
    {
        $this->ensureTableExists();
        $platform = $this->connection->getDatabasePlatform();
        $sql = sprintf('SELECT %s, %s FROM %s',
            $platform->quoteIdentifier('key'),
            $platform->quoteIdentifier('value'),
            $platform->quoteIdentifier($this->tableName)
        );
        $result = $this->connection->fetchAllAssociative($sql);

        $data = [];
        foreach ($result as $row) {
            $data[$row['key']] = json_decode($row['value'], associative: true);
        }
        return $data;
    }

    public function clear(): void
    {
        $this->ensureTableExists();
        $platform = $this->connection->getDatabasePlatform();
        $sql = sprintf('DELETE FROM %s', $platform->quoteIdentifier($this->tableName));
        $this->connection->executeStatement($sql);
    }

    protected function ensureTableExists(): void
    {
        if ($this->autoCreateTable) {
            $this->createTableIfNotExists();
        }
    }

    private function createTableIfNotExists(): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist([$this->tableName])) {
            $table = new Table($this->tableName);
            $table->addColumn('key', 'string', ['length' => 255]);
            $table->addColumn('value', 'text');
            $table->setPrimaryKey(['key']);
            $schemaManager->createTable($table);
        }
    }
}