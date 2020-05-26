<?php

namespace RL\SARSA;

use RL\ActionSpace;
use RL\Agent as RLAgent;
use RL\Environment;

/**
 * A SARSA agent
 */
class Agent implements RLAgent
{
    private SARSA $sarsa;

    public function __construct(SARSA $sarsa)
    {
        $this->sarsa = $sarsa;
    }

    public function act(Environment $env): void
    {
        $state = $env->getState();
        $actionId = $this->sarsa->act($state);
        $reward = $env->act($actionId);

        $this->sarsa->learn($state, $actionId, $reward, $env->getState(), $env->isDone());
    }

    public function pickAction(\RL\State $state): int
    {
        return $this->sarsa->act($state);
    }
}
