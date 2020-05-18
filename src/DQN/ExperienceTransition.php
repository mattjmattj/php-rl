<?php

namespace RL\DQN;

use RL\State;

class ExperienceTransition
{
    public State $previousState;
    public int $actionId;
    public float $reward;
    public State $nextState;
    public bool $done;

    public function __construct(
        State $previousState,
        int $actionId,
        float $reward,
        State $nextState,
        bool $done
    ) {
        $this->previousState = $previousState;
        $this->actionId = $actionId;
        $this->reward = $reward;
        $this->nextState = $nextState;
        $this->done = $done;
    }
}
