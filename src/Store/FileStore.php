<?php


namespace LukasJankowski\Storage\Store;

use LukasJankowski\Storage\Condition\FileCondition;
use LukasJankowski\Storage\Repository\FileRepository;
use LukasJankowski\Storage\Repository\RepositoryInterface;
use Monolog\Logger;

class FileStore implements StoreInterface
{
    /** @var array - The configuration for the database */
    private $config;

    /** @var Logger - The logger for this store */
    private $logger;

    /** @var string - The identifier to separate the records */
    private $identifier;

    /** @var array - The file used as storage */
    private $file;

    /** @var array - The data to modify */
    private $data;

    /** @var string - The prefix for this specific class */
    private $logPrefix = 'LukasJankowski\Storage.Store.FileStore:';

    /**
     * DatabaseStore constructor.
     *
     * @param array $config
     * @param Logger $logger
     *
     * @throws \ErrorException|\InvalidArgumentException
     */
    public function __construct(array $config, Logger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;

        $this->ensureWorkingCondition();

        $this->file = $this->config['path'];
        $this->identifier = $this->config['identifier'] ?? 'default';

        $this->setupFile();
    }

    /**
     * Ensure that this store can properly be use the database
     *
     * @throws \ErrorException|\InvalidArgumentException
     */
    private function ensureWorkingCondition(): void
    {
        $condition = new FileCondition($this->logger);
        $condition->setConfig($this->config);
        $condition->check();
    }

    /**
     * Load the contents into the variable to use it for the lifetime
     * of the application without multiple read/write calls.
     *
     * @throws \ErrorException
     */
    private function setupFile(): void
    {
        $contents = file_get_contents($this->file);
        if ($contents === false) {
            $this->logger->error(
                sprintf('%s The content of the file could not be read.', $this->logPrefix)
            );

            throw new \ErrorException('The content could not be received.');
        }

        $data = json_decode($contents, true);
        if ($data === null && $contents !== '') {
            $this->logger->error(
                sprintf('%s The content of the file is not valid JSON.', $this->logPrefix)
            );

            throw new \ErrorException('The content is not JSON.');
        }

        $this->logger->debug(
            sprintf('%s Store successfully loaded from file.', $this->logPrefix)
        );

        $this->data = $data;
    }

    /**
     * Get the repository of this store.
     *
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface
    {
        return new FileRepository($this, $this->logger);
    }

    /**
     * Save the store data to the file.
     *
     * @throws \ErrorException
     */
    public function saveToFile(): bool
    {
        $contents = json_encode($this->data);

        $isSuccessful = file_put_contents($this->file, $contents);

        if ($isSuccessful === false) {
            $this->logger->error(
                sprintf('%s The data could not be written to the file.', $this->logPrefix)
            );

            throw new \ErrorException('Unable to write data to file.');
        }

        $this->logger->debug(
            sprintf('%s Store successfully saved to file.', $this->logPrefix)
        );

        return $isSuccessful;
    }

    /**
     * Get a record from the store.
     *
     * @param $offset
     *
     * @return mixed
     */
    public function get($offset)
    {
        return $this->data[$this->identifier][$offset] ?? null;
    }

    /**
     * Get all records from the store.
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->data[$this->identifier] ?? null;
    }

    /**
     * Insert a record into the store
     *
     * @param $offset
     * @param $value
     *
     * @return bool
     */
    public function set($offset, $value): bool
    {
        if (!isset($this->data[$this->identifier])) {
            $this->data[$this->identifier] = [];
        }

        $this->data[$this->identifier][$offset] = $value;

        return true;
    }

    /**
     * Remove a record from the store
     *
     * @param $offset
     *
     * @return bool
     */
    public function unset($offset): bool
    {
        unset($this->data[$this->identifier][$offset]);

        return true;
    }
}
