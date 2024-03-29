<?php

namespace LukasJankowski\Storage\Store;

use PDO;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use LukasJankowski\Storage\Condition\DatabaseCondition;
use LukasJankowski\Storage\Repository\DatabaseRepository;
use LukasJankowski\Storage\Repository\RepositoryInterface;

class DatabaseStore implements StoreInterface
{
    /** @var array - The configuration for the database */
    private $config;

    /** @var string - The table used for this storage */
    private $table;

    /** @var string - The identifier to separate the records */
    private $identifier;

    /** @var Connection - The connection to the database */
    private $connection;

    /**
     * DatabaseStore constructor.
     *
     * @param array $config
     *
     * @throws DBALException|\ErrorException|\InvalidArgumentException
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->setupConnection();
        $this->ensureWorkingCondition();

        $this->table = $this->config['tableName'];
        $this->identifier = $this->config['identifier'] ?? 'default';
    }

    /**
     * Try to establish the connection to the database.
     *
     * @throws DBALException
     */
    private function setupConnection(): void
    {
        $conn = DriverManager::getConnection($this->config, new Configuration);
        if (!$conn->connect()) {
            throw new DBALException('Connection could not be established.');
        }

        $this->connection = $conn;
    }

    /**
     * Ensure that this store can properly be use the database
     *
     * @throws \ErrorException|\InvalidArgumentException
     */
    private function ensureWorkingCondition(): void
    {
        $condition = new DatabaseCondition();
        $condition->setConnection($this->connection);
        $condition->setConfig($this->config);
        $condition->check();
    }

    /**
     * The queryBuilder must be reset after every database call
     *
     */
    private function queryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    /**
     * Getter. The repository for this store.
     *
     * @return DatabaseRepository
     */
    public function getRepository(): RepositoryInterface
    {
        return new DatabaseRepository($this);
    }

    /**
     * Get a single record from the database.
     *
     * @param $offset
     *
     * @return mixed
     */
    public function get($offset)
    {
        $stmt = $this->queryBuilder()
            ->select('`id`', '`key`', '`val`')
            ->from($this->table)
            ->where('`sid` = :identifier')
            ->andWhere('`key` = :offset')
            ->setParameter('identifier', $this->identifier)
            ->setParameter('offset', $offset)
            ->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all records associated with the identifier from the database.
     *
     * @return mixed[]
     */
    public function getAll(): array
    {
        $stmt = $this->queryBuilder()
            ->select('`key`', '`val`')
            ->from($this->table)
            ->where('`sid` = :identifier')
            ->setParameter('identifier', $this->identifier)
            ->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insert a record into the database.
     *
     * @param $offset
     * @param $value
     *
     * @return bool - Whether the insert was successful or not
     */
    public function set($offset, $value): bool
    {
        $this->unset($offset);

        $count = $this->queryBuilder()
            ->insert($this->table)
            ->setValue('`sid`', ':identifier')
            ->setValue('`key`', ':offset')
            ->setValue('`val`', ':value')
            ->setParameter('identifier', $this->identifier)
            ->setParameter('offset', $offset)
            ->setParameter('value', $value)
            ->execute();

        return $count > 0;
    }

    /**
     * Delete a record from the database
     *
     * @param $offset
     *
     * @return bool - Whether it deleted a record or not
     */
    public function unset($offset): bool
    {
        $count = $this->queryBuilder()
            ->delete($this->table)
            ->where('`sid` = :identifier')
            ->andWhere('`key` = :offset')
            ->setParameter('identifier', $this->identifier)
            ->setParameter('offset', $offset)
            ->execute();

        return $count > 0;
    }
}
