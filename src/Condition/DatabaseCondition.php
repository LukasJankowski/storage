<?php

namespace LukasJankowski\Storage\Condition;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Comparator;

class DatabaseCondition implements ConditionInterface
{
    /** @var array - The configuration for the database */
    private $config;

    /** @var Connection - The connection to the database */
    private $connection;

    /**
     * Setter. Set the connection.
     *
     * @param Connection $connection
     */
    public function setConnection(Connection $connection): void
    {
        $this->connection = $connection;
    }

    /**
     * Setter. Set the config.
     *
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * Check if the connection can be used.
     *
     * @throws \ErrorException
     */
    public function check(): void
    {
        $this->testConfigurationSufficient();
        $this->testTableExists();
        $this->testTableSchema();
    }

    /**
     * Check if the provided configuration is sufficient for the store.
     *
     * @throws \InvalidArgumentException
     */
    private function testConfigurationSufficient(): void
    {
        if (!isset($this->config['tableName'])) {
            throw new \InvalidArgumentException('No table-name set.');
        }
    }

    /**
     * Check if the table to use exists
     *
     * @throws \InvalidArgumentException
     */
    private function testTableExists(): void
    {
        $schemaManager = $this->connection->getSchemaManager();
        if (!$schemaManager->tablesExist([$this->config['tableName']])) {
            throw new \InvalidArgumentException('No table with the supplied name exists.');
        }
    }

    /**
     * Check if the table can be used safely.
     *
     * @throws \ErrorException
     */
    private function testTableSchema(): void
    {
        $comparator = new Comparator;
        $expectedSchema = require __DIR__ . '/../resources/database_migration.php';

        $existingSchema = $this->connection
              ->getSchemaManager()
              ->createSchema();

        $differsFromExpected = count($comparator->compare($existingSchema, $expectedSchema)->changedTables) > 0;

        if ($differsFromExpected) {
            throw new \ErrorException('The table schema differs from the expected schema.');
        }
    }
}
