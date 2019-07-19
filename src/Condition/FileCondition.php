<?php


namespace LukasJankowski\Storage\Condition;


use ErrorException;
use InvalidArgumentException;
use Monolog\Logger;

class FileCondition implements ConditionInterface
{
    /** @var Logger - The logger for this store */
    private $logger;

    /** @var array - The configuration for the file */
    private $config;

    /** @var string - The prefix for this specific class */
    private $logPrefix = 'LukasJankowski\Storage.Condition.FileCondition:';

    /**
     * ConditionInterface constructor.
     *
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
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
     * Check whether the store is usable or not.
     *
     * @throws ErrorException
     */
    public function check(): void
    {
        $this->testConfigurationSufficient();
        $this->testFileExists();
        $this->testFileHasReadWriteAccess();
    }

    /**
     * Check if the provided configuration is sufficient for the store.
     *
     * @throws InvalidArgumentException
     */
    private function testConfigurationSufficient(): void
    {
        if (!isset($this->config['identifier'])) {
            $this->logger->debug(
                sprintf('%s No identifier specified, all records are accessible.', $this->logPrefix)
            );
        }

        if (!isset($this->config['path'])) {
            $this->logger->error(sprintf('%s The path argument must be set.', $this->logPrefix));
            throw new InvalidArgumentException('No path to the file set.');
        }
    }

    /**
     * Check if the file exists.
     *
     * @throws InvalidArgumentException
     */
    private function testFileExists(): void
    {
        if (!file_exists($this->config['path'])) {
            $this->logger->error(
                sprintf(
                    '%s No file "%s" exists. Consider creating it first.',
                    $this->logPrefix,
                    $this->config['path']
                )
            );
            throw new InvalidArgumentException('No file with the supplied name exists.');
        }

        $this->logger->debug(sprintf('%s Supplied file exists.', $this->logPrefix));
    }

    /**
     * Check if the file is read/writable.
     *
     * @throws ErrorException
     */
    private function testFileHasReadWriteAccess(): void
    {
        if (!is_readable($this->config['path']) || !is_writable($this->config['path'])) {
            $this->logger->error(sprintf('%s The used file is missing the required permissions.', $this->logPrefix));
            throw new ErrorException('The permissions for the file are unacceptable.');
        }
    }
}