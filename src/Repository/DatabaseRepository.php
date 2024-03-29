<?php

namespace LukasJankowski\Storage\Repository;

use LukasJankowski\Storage\Store\StoreInterface;

class DatabaseRepository implements RepositoryInterface
{
    /** @var StoreInterface - The database store to use */
    private $store;

    /**
     * DatabaseRepository constructor.
     *
     * @param StoreInterface $store
     */
    public function __construct(StoreInterface $store)
    {
        $this->store = $store;
    }

    /**
     * Persist the data to the store. May or may not be of actual use
     * depending on the type of store.
     *
     * @return bool
     */
    public function persist(): bool
    {
        // No caching for now and no extra call needed because of that.
        return true;
    }

    /**
     * Check if a record exists in the store.
     *
     * @param $offset
     *
     * @return bool
     */
    public function exists($offset): bool
    {
        return $this->get($offset) !== null;
    }

    /**
     * Get a record from the store.
     *
     * @param $offset
     *
     * @return mixed|null
     */
    public function get($offset)
    {
        return $this->store->get($offset)['val'] ?? null;
    }

    /**
     * Insert a record into the store
     *
     * @param $offset
     * @param $value
     */
    public function set($offset, $value): void
    {
        $this->store->set($offset, $value);
    }

    /**
     * Remove a record from the store.
     *
     * @param $offset
     */
    public function unset($offset): void
    {
        $this->store->unset($offset);
    }

    /**
     * Convert the stored data to JSON
     *
     * @return string
     */
    public function toString(): string
    {
        return json_encode($this->store->getAll());
    }
}
