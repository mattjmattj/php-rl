<?php

namespace RL\SARSA;

interface Policy
{
    /**
     * Picks an action
     * @param array $qstate - the Q values for each action in the considered state
     * @return array - a tuple (int actionId, float reward)
     */
    public function apply(array $qstate): array;
}
