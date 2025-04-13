<?php

namespace Nkamuo\Barcode\Storage;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\DriverManager;

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
    public function __construct(Connection $connection, array $config = [],  string $tableName = 'barcode_hash_table', bool $autoCreateTable = true)
    {
        $this->connection = $connection;//DriverManager::getConnection($dbConfig);
        $this->tableName = $tableName;
        $this->autoCreateTable = $autoCreateTable;

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
        $schemaManager = $this->connection->createSchemaManager();
        if (!$schemaManager->tablesExist([$this->tableName])) {
            $this->connection->executeStatement(sprintf(
                'CREATE TABLE %s (
                    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
                    `value` TEXT NOT NULL
                )',
                $this->tableName
            ));
        }
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
        $result = $this->connection->fetchOne(
            sprintf('SELECT value FROM %s WHERE `key` = ?', $this->tableName),
            [$key]
        );

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
        $this->connection->executeStatement(
            sprintf(
                'REPLACE INTO %s (`key`, `value`) VALUES (?, ?)',
                $this->tableName
            ),
            [$key, json_encode($value)]
        );
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
        $count = $this->connection->executeStatement(
            sprintf('DELETE FROM %s WHERE `key` = ?', $this->tableName),
            [$key]
        );

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
        $result = $this->connection->fetchOne(
            sprintf('SELECT 1 FROM %s WHERE `key` = ?', $this->tableName),
            [$key]
        );

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
        $result = $this->connection->fetchAllAssociative(
            sprintf('SELECT `key`, `value` FROM %s', $this->tableName)
        );

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
        $this->connection->executeStatement(
            sprintf('DELETE FROM %s', $this->tableName)
        );
    }
}