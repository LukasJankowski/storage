<?php

namespace LukasJankowski\Storage\Store;

use LukasJankowski\Storage\Condition\FileCondition;
use LukasJankowski\Storage\Repository\FileRepository;
use LukasJankowski\Storage\Repository\RepositoryInterface;

class FileStore implements StoreInterface
{
    /** @var array - The configuration for the database */
    private $config;

    /** @var string - The identifier to separate the records */
    private $identifier;

    /** @var array - The file used as storage */
    private $file;

    /** @var array - The data to modify */
    private $data;

    /**
     * DatabaseStore constructor.
     *
     * @param array $config
     *
     * @throws \ErrorException|\InvalidArgumentException
     */
    public function __construct(array $config)
    {
        $this->config = $config;

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
        $condition = new FileCondition;
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
            throw new \ErrorException('The content could not be received.');
        }

        $data = json_decode($contents, true);
        if ($data === null && $contents !== '') {
            throw new \ErrorException('The content is not JSON.');
        }

        $this->data = $data;
    }

    /**
     * Get the repository of this store.
     *
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface
    {
        return new FileRepository($this);
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
            throw new \ErrorException('Unable to write data to file.');
        }

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
