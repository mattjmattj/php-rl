<?php

namespace RL\QLearning;

use Closure;
use RL\SARSA\SARSA;
use RL\ActionSet;
use RL\SARSA\Policy;
use RL\State;

/**
 * A basic implementation of a Q-table
 * We use a SARSA with a "max" policy
 */
class QTable extends SARSA
{
    /**
     * @param ActionSet $actionSet - the ActionSet of the system
     * @param float $learningRate - the learning rate (default: 1.0)
     * @param float $discountFactor - (optional) the discount factor of the Bellman equation (default: 0.995)
     * @param Closure $initialize - (optional) the callable to call when initializing a cell (default : fn() => 0)
     * @param array $q - (optional) the pre-filled table
     */
    public function __construct(
        ActionSet $actionSet,
        float $learningRate = 1.0,
        float $discountFactor = 0.995,
        ?Closure $initializer = null,
        array $q = []
    ) {
        parent::__construct(
            $actionSet,
            new class implements Policy {
                public function apply(array $qstate): array
                {
                    $reward = max($qstate);
                    $actionId = array_search($reward, $qstate);
                    return [$actionId, $reward];
                }
            },
            $learningRate,
            $discountFactor,
            $initializer,
            $q
        );
    }
}
