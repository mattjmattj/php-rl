<?php

namespace RL\DQN;

use RL\State;

interface Model
{
    /**
     * Computes a prediction according to the given State
     * @return array a Q array indexed by action ids
     */
    public function predict(State $state): array;

    /**
     * Computes a prediction only for one action
     */
    public function predictOne(State $state, int $actionId): float;

    /**
     * Updates the model Q prediction for the given action
     * @return float the loss
     */
    public function fit(State $state, int $actionId, float $reward): float;

    public function fitBatch(array $states, array $actionIds, array $rewards): float;
}
