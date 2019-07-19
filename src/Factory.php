<?php

namespace LukasJankowski\Storage;

use Doctrine\DBAL\DBALException;
use ErrorException;
use InvalidArgumentException;
use LukasJankowski\Storage\Store\DatabaseStore;
use LukasJankowski\Storage\Store\FileStore;
use LukasJankowski\Storage\Store\RedisStore;
use LukasJankowski\Storage\Store\StoreInterface;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

class Factory
{
    /** @var Logger - The logger for this class */
    private static $logger;

    /** @var StoreInterface - The store to use */
    private static $store;

    /** @var array - The configuration for this package */
    private static $config;

    /** @var string - The prefix for the logger */
    private static $logPrefix = 'LukasJankowski\Storage.Factory:';

    /** @var string - The name of the logger for the package */
    private static $loggerName = 'lukasjankowski.storage';

    /**
     * Create the storage variable for further use.
     *
     * @param array $config
     *
     * @return Storage
     * @throws InvalidArgumentException|DBALException|ErrorException
     */
    public static function createStorage(array $config = []): Storage
    {
        self::$config = $config;

        self::setupLogger();
        self::createStore();

        return new Storage(self::$store->getRepository(), self::$logger);
    }

    /**
     * Setup the logging. Using an existing logger is preferred.
     *
     * @throws InvalidArgumentException
     */
    private static function setupLogger(): void
    {
        self::$config['log'] = self::$config['log']
            ?? new Logger(self::$loggerName);

        if (!(self::$config['log'] instanceof Logger)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s Logger must be of type Monolog\Logger',
                    self::$logPrefix
                )
            );
        }

        if (self::$config['log']->getName() === self::$loggerName) {
            self::$config['log']->pushHandler(new NullHandler);
        }

        self::$logger = self::$config['log'];
    }

    /**
     * Create a store from the passed configuration.
     *
     * @throws InvalidArgumentException|ErrorException|DBALException
     */
    private static function createStore(): void
    {
        $store = self::$config['store'] ?? 'database';
        $config = self::$config['storeConfig'] ?? null;

        if ($store instanceof StoreInterface) {
            self::$store = $store;
        } elseif ($store === 'database') {
            self::$store = new DatabaseStore($config, self::$logger);
        } elseif ($store === 'redis') {
            self::$store = new RedisStore($config, self::$logger);
        } elseif ($store === 'file') {
            self::$store = new FileStore($config, self::$logger);
        } else {
            self::$logger->error(
                sprintf(
                    '%s Storage type not supported',
                    self::$logPrefix
                )
            );
            throw new InvalidArgumentException(
                'Storage type not supported.'
            );
        }
    }
}
