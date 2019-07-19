<?php


namespace LukasJankowski\Storage\Condition;

use Monolog\Logger;

interface ConditionInterface
{
    /**
     * ConditionInterface constructor.
     *
     * @param Logger $logger
     */
    public function __construct(Logger $logger);

    /**
     * Check whether the store is usable or not.
     *
     */
    public function check(): void;
}
