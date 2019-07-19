<?php

namespace LukasJankowski\Storage\Store;

use LukasJankowski\Storage\Repository\RepositoryInterface;

interface StoreInterface
{
    /**
     * StoreInterface constructor.
     *
     * @param array $config
     */
    public function __construct(array $config);

    /**
     * Get the repository for the store.
     *
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface;

    /**
     * Get a record from the store.
     *
     * @param $offset
     *
     * @return mixed
     */
    public function get($offset);

    /**
     * Get all records from the store.
     *
     * @return array
     */
    public function getAll(): array;

    /**
     * Insert a record into the store
     *
     * @param $offset
     * @param $value
     *
     * @return bool
     */
    public function set($offset, $value): bool;

    /**
     * Remove a record from the store
     *
     * @param $offset
     *
     * @return bool
     */
    public function unset($offset): bool;
}
