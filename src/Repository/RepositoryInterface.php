<?php

namespace LukasJankowski\Storage\Repository;

use LukasJankowski\Storage\Store\StoreInterface;

interface RepositoryInterface
{
    /**
     * RepositoryInterface constructor.
     *
     * @param StoreInterface $store
     */
    public function __construct(StoreInterface $store);

    /**
     * Persist the data to the store. May or may not be of actual use
     * depending on the type of store.
     *
     * @return bool
     */
    public function persist(): bool;

    /**
     * Check if a record exists in the store.
     *
     * @param $offset
     *
     * @return bool
     */
    public function exists($offset): bool;

    /**
     * Get a record from the store.
     *
     * @param $offset
     *
     * @return mixed|null
     */
    public function get($offset);

    /**
     * Insert a record into the store
     *
     * @param $offset
     * @param $value
     */
    public function set($offset, $value): void;

    /**
     * Remove a record from the store.
     *
     * @param $offset
     */
    public function unset($offset): void;

    /**
     * Convert the stored data to JSON
     *
     * @return false|string
     */
    public function toString(): string;
}
