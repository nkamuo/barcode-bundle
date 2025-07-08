<?php

namespace Nkamuo\Barcode\Storage;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;

class DBALHashStorage implements HashStorageInterface
{
    private Connection $connection;
    private string $tableName;
    private bool $autoCreateTable;

    /**
     * Constructor initializes the Doctrine DBAL storage.
     *
     * @param string $tableName The name of the table to store key-value pairs.
     * @param bool $autoCreateTable Whether to create the table if it doesn't exist.
     * @throws Exception
     */
    public function __construct(Connection $connection, array $config = [], string $tableName = 'barcode_hash_table', bool $autoCreateTable = true)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->autoCreateTable = $autoCreateTable;
    }

    /**
     * Get a value by its key.
     *
     * @param string $key
     * @return mixed|null
     * @throws Exception
     */
    public function get(string $key): mixed
    {
        $this->ensureTableExists();
        $platform = $this->connection->getDatabasePlatform();
        $sql = sprintf(
            'SELECT %s FROM %s WHERE %s = ?',
            $platform->quoteIdentifier('value'),
            $platform->quoteIdentifier($this->tableName),
            $platform->quoteIdentifier('key')
        );
        $result = $this->connection->fetchOne($sql, [$key]);
        return $result !== false ? json_decode($result, associative: true) : null;
    }

    /**
     * Save or update a value by its key.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     * @throws Exception
     */
    public function set(string $key, mixed $value): void
    {
        $this->ensureTableExists();
        $platform = $this->connection->getDatabasePlatform();
        $table = $platform->quoteIdentifier($this->tableName);
        $keyCol = $platform->quoteIdentifier('key');
        $valueCol = $platform->quoteIdentifier('value');

        if ($platform instanceof PostgreSQLPlatform) {
            $sql = sprintf(
                'INSERT INTO %s (%s, %s) VALUES (?, ?) ON CONFLICT (%s) DO UPDATE SET %s = EXCLUDED.%s',
                $table,
                $keyCol,
                $valueCol,
                $keyCol,
                $valueCol,
                $valueCol
            );
        } else {
            // MySQL and SQLite support REPLACE INTO
            $sql = sprintf(
                'REPLACE INTO %s (%s, %s) VALUES (?, ?)',
                $table,
                $keyCol,
                $valueCol
            );
        }
        $this->connection->executeStatement($sql, [$key, json_encode($value)]);
    }

    /**
     * Delete a key-value pair.
     *
     * @param string $key
     * @return bool
     * @throws Exception
     */
    public function delete(string $key): bool
    {
        $this->ensureTableExists();
        $platform = $this->connection->getDatabasePlatform();
        $sql = sprintf(
            'DELETE FROM %s WHERE %s = ?',
            $platform->quoteIdentifier($this->tableName),
            $platform->quoteIdentifier('key')
        );
        $count = $this->connection->executeStatement($sql, [$key]);
        return $count > 0;
    }

    /**
     * Check if a key exists.
     *
     * @param string $key
     * @return bool
     * @throws Exception
     */
    public function has(string $key): bool
    {
        $this->ensureTableExists();
        $platform = $this->connection->getDatabasePlatform();
        $sql = sprintf(
            'SELECT 1 FROM %s WHERE %s = ?',
            $platform->quoteIdentifier($this->tableName),
            $platform->quoteIdentifier('key')
        );
        $result = $this->connection->fetchOne($sql, [$key]);
        return $result !== false;
    }

    /**
     * Retrieve all key-value pairs.
     *
     * @return array
     * @throws Exception
     */
    public function all(): array
    {
        $this->ensureTableExists();
        $platform = $this->connection->getDatabasePlatform();
        $sql = sprintf(
            'SELECT %s, %s FROM %s',
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

    /**
     * Clear all key-value pairs from storage.
     *
     * @return void
     * @throws Exception
     */
    public function clear(): void
    {
        $this->ensureTableExists();
        $platform = $this->connection->getDatabasePlatform();
        $sql = sprintf('DELETE FROM %s', $platform->quoteIdentifier($this->tableName));
        $this->connection->executeStatement($sql);
    }

    /**
     * @throws Exception
     */
    protected function ensureTableExists(): void
    {
        if ($this->autoCreateTable) {
            $this->createTableIfNotExists();
        }
    }

    /**
     * Create the key-value store table if it doesn't exist.
     *
     * @return void
     * @throws Exception
     */
    private function createTableIfNotExists(): void
    {
        $tableName = $this->tableName;
        $platform = $this->connection->getDatabasePlatform();

        if ($platform instanceof PostgreSQLPlatform) {
            // Check existence in PostgreSQL
            $exists = (bool) $this->connection->fetchOne(
                "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_name = :table",
                ['table' => $tableName]
            );
            if (!$exists) {
                $this->connection->executeStatement(sprintf(
                    'CREATE TABLE %s (
                        %s VARCHAR(255) PRIMARY KEY,
                        %s TEXT NOT NULL
                    )',
                    $platform->quoteIdentifier($tableName),
                    $platform->quoteIdentifier('key'),
                    $platform->quoteIdentifier('value')
                ));
            }
        } else {
            // Default: MySQL and others
            $exists = (bool) $this->connection->fetchOne(
                "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :table",
                ['table' => $tableName]
            );
            if (!$exists) {
                $this->connection->executeStatement(sprintf(
                    'CREATE TABLE %s (
                        %s VARCHAR(255) NOT NULL PRIMARY KEY,
                        %s TEXT NOT NULL
                    )',
                    $platform->quoteIdentifier($tableName),
                    $platform->quoteIdentifier('key'),
                    $platform->quoteIdentifier('value')
                ));
            }
        }
    }
}
