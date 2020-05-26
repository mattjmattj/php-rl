<?php

namespace RL\DQN;

use Psr\Log\LoggerInterface;
use RL\ActionSpace;
use RL\Environment;
use RL\State;

/**
 * An epsilon-greedy DQN agent
 */
class EGreedyAgent extends AbstractAgent
{
    /** maximum precision when using rand() */
    const RAND_PRECISION = 1e9;

    protected float $epsilon;

    public function __construct(
        ModelProvider $modelProvider,
        float $discountFactor,
        float $epsilon,
        ExperienceReplayer $replayer,
        int $updateTargetModelInterval,
        Environment $env,
        bool $useDoubleDQN = true,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct(
            $modelProvider,
            $discountFactor,
            $replayer,
            $updateTargetModelInterval,
            $env,
            $useDoubleDQN,
            $logger
        );
        $this->epsilon = $epsilon;
    }

    protected function chooseRandomAction(State $state): int
    {
        return array_rand($this->env->getActionSpace()->getActions());
    }

    public function pickAction(State $state): int
    {
        if (rand(0, self::RAND_PRECISION) / self::RAND_PRECISION < $this->epsilon) {
            $this->logger->debug('choosing random action');
            return $this->chooseRandomAction($state);
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
