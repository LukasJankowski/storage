<?php

namespace LukasJankowski\Storage;

use Doctrine\DBAL\DBALException;
use LukasJankowski\Storage\Store\DatabaseStore;
use LukasJankowski\Storage\Store\FileStore;
use LukasJankowski\Storage\Store\StoreInterface;

class Factory
{
    /** @var StoreInterface - The store to use */
    private static $store;

    /** @var array - The configuration for this package */
    private static $config;

    /**
     * Create the storage variable for further use.
     *
     * @param array $config
     *
     * @return Storage
     * @throws \InvalidArgumentException|DBALException|\ErrorException
     */
    public static function createStorage(array $config = []): Storage
    {
        self::$config = $config;

        self::createStore();

        return new Storage(self::$store->getRepository());
    }

    /**
     * Create a store from the passed configuration.
     *
     * @throws \InvalidArgumentException|\ErrorException|DBALException
     */
    private static function createStore(): void
    {
        $store = self::$config['store'] ?? 'database';
        $config = self::$config['storeConfig'] ?? null;

        if ($store instanceof StoreInterface) {
            self::$store = $store;
        } elseif ($store === 'database') {
            self::$store = new DatabaseStore($config);
        } elseif ($store === 'file') {
            self::$store = new FileStore($config);
        } else {
            throw new \InvalidArgumentException('Storage type not supported.');
        }
    }
}
