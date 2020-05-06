<?php

namespace RL;

/**
 * An RL Environment
 */
interface Environment
{
    /**
     *
     */
    public function getActionSet(): ActionSet;

    /**
     *
     */
    public function getState(): State;

    /**
     * Perform an action on the environment
     * @param int $actionId - the id of an action to perform
     * @return float - the immediate reward of $actionId
     */
    public function act(int $actionId): float;

    /**
     *
     */
    public function isDone(): bool;

    public function reset(): void;
}
