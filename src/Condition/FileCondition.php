<?php

namespace LukasJankowski\Storage\Condition;

class FileCondition implements ConditionInterface
{
    /** @var array - The configuration for the file */
    private $config;

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
     * @throws \ErrorException
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
     * @throws \InvalidArgumentException
     */
    private function testConfigurationSufficient(): void
    {
        if (!isset($this->config['path'])) {
            throw new \InvalidArgumentException('No path to the file set.');
        }
    }

    /**
     * Check if the file exists.
     *
     * @throws \InvalidArgumentException
     */
    private function testFileExists(): void
    {
        if (!file_exists($this->config['path'])) {
            throw new \InvalidArgumentException('No file with the supplied name exists.');
        }
    }

    /**
     * Check if the file is read/writable.
     *
     * @throws \ErrorException
     */
    private function testFileHasReadWriteAccess(): void
    {
        if (!is_readable($this->config['path']) || !is_writable($this->config['path'])) {
            throw new \ErrorException('The permissions for the file are unacceptable.');
        }
    }
}
