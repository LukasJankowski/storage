<?php

namespace LukasJankowski\Storage\Condition;

interface ConditionInterface
{
    /**
     * Check whether the store is usable or not.
     *
     */
    public function check(): void;
}
