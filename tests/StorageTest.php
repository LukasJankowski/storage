<?php

use LukasJankowski\Storage\Storage;

class StorageTest extends PHPUnit\Framework\TestCase
{
    public function testNonExistingItemReturnsNull()
    {
        // 1. Setup
        $config = [
            'store' => 'file',
            'storeConfig' => [
                'identifier' => 'custom',
                'path' => 'src/resources/store.json'
            ]
        ];
        // 2. Execution
        $storage = \LukasJankowski\Storage\Factory::createStorage($config);
        // 3. Assertion
        $this->assertNull($storage['non-existing-key']);
        $this->assertFalse(isset($storage['non-existing-key']));
    }

    public function testThrowsExceptionOnNonExistingStorage()
    {
        $config = [
            'store' => 'non-existing',
            'storeConfig' => [
                'identifier' => 'custom',
                'path' => 'src/resources/store.json'
            ]
        ];

        $this->expectExceptionMessage('Storage type not supported.');

        $storage = \LukasJankowski\Storage\Factory::createStorage($config);

    }
}
