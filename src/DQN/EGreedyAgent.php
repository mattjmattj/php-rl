<?php

namespace RL\DQN;

use RL\ActionSet;
use RL\Environment;
use RL\State;

/**
 * An epsilon-greedy DQN agent
 */
class EGreedyAgent extends AbstractAgent
{
    protected float $epsilon;

    public function __construct(
        ModelProvider $modelProvider,
        float $discountFactor,
        float $epsilon,
        ExperienceReplayer $replayer,
        int $updateTargetModelInterval,
        Environment $env
    ) {
        parent::__construct($modelProvider, $discountFactor, $replayer, $updateTargetModelInterval, $env);
        $this->epsilon = $epsilon;
    }

    protected function chooseRandomAction(): int
    {
        return array_rand($this->env->getActionSet()->getActionIds());
    }

    public function chooseAction(State $state): int
    {
        if (rand(0, 1000000) / 1000000.0 < $this->epsilon) {
            $this->logger->debug('choosing random action');
            return $this->chooseRandomAction();
        } else {
            $qstate = $this->modelPredict($state);
            $q = max($qstate);
            $action = array_search($q, $qstate);
            $this->logger->debug("choosing action #$action, q=$q");
            return $action;
        }
    }
    
    public function getEpsilon(): float
    {
        return $this->epsilon;
    }

    public function setEpsilon(float $epsilon): void
    {
        $this->epsilon = $epsilon;
    }
}
