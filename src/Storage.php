<?php

namespace LukasJankowski\Storage;

use ArrayAccess;
use LukasJankowski\Storage\Repository\RepositoryInterface;
use Monolog\Logger;

class Storage implements ArrayAccess
{
    /** @var RepositoryInterface - The repository to access */
    private $repository;

    /** @var Logger - The logger to use */
    private $logger;

    /** @var string - The prefix for the logger */
    private $logPrefix = 'LukasJankowski\Storage.Storage:';

    /**
     * Storage constructor.
     *
     * @param RepositoryInterface $repository
     * @param Logger $logger
     */
    public function __construct(RepositoryInterface $repository, Logger $logger)
    {
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * Persist the data to the storage. This can be a no-op or required
     * depending on the type of store used for the Storage instance.
     *
     *
     * @return bool - Whether the persisting was successful or not
     */
    public function persist(): bool
    {
        return $this->repository->persist();
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset
     *
     * @return bool true on success or false on failure
     */
    public function offsetExists($offset): bool
    {
        return $this->repository->exists($offset);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->repository->get($offset);
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->repository->set($offset, $value);
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        $this->repository->unset($offset);
    }

    /**
     * Convert the store to a string (JSON).
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->repository->toString();
    }
}
