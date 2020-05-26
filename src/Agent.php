<?php

namespace RL;

/**
 * An RL Agent
 */
interface Agent
{
    /**
     * Performs one action on a given environment
     */
    public function act(Environment $env): void;

    /**
     * Shortcut for simply choosing an action based on a state
     */
    public function pickAction(State $state): int;
}
