<?php

namespace RL\QLearning;

use Closure;
use RL\ActionSet;
use RL\State;

/**
 * A basic implementation of a Q-table
 */
class QTable
{
    private array $q;

    /** alpha */
    private float $learningRate;

    /** gamma */
    private float $discountFactor;

    private ActionSet $actionSet;

    private Closure $initializer;

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
        $this->actionSet = $actionSet;
        $this->learningRate = $learningRate;
        $this->discountFactor = $discountFactor;
        $this->initializer = $initializer ?? fn () => 0;
        $this->q = $q;
    }

    public function act(State $state): int
    {
        $stateUid = $this->initializeQForState($state);

        return array_search(max($this->q[$stateUid]), $this->q[$stateUid]);
    }

    public function learn(State $origin, int $actionId, float $reward, State $next, bool $done): void
    {
        $originUid = $this->initializeQForState($origin);

        if ($done) {
            // when we are done, the reward is known
            $q = $reward;
        } else {
            $nextUid = $this->initializeQForState($next);

            // otherwise, we apply the Bellman equation to update the expected Q
            // Q(s,a) := Q(s,a) + α(r + γmax(Q(s',*) - Q(s,a))
            $q = $this->q[$originUid][$actionId];
            $a = $this->learningRate;
            $g = $this->discountFactor;
            $q = $q + $a * ($reward + $g * max($this->q[$nextUid]) -$q);
        }
        $this->q[$originUid][$actionId] = $q;
    }

    public function getTable(): array
    {
        return $this->q;
    }

    private function initializeQForState(State $state): string
    {
        $stateUid = $state->uid();

        if (isset($this->q[$stateUid])) {
            return $stateUid;
        }

        $this->q[$stateUid] = [];
        foreach ($this->actionSet->getActionIds() as $actionId) {
            $this->q[$stateUid][$actionId] = ($this->initializer)($stateUid, $actionId);
        }

        return $stateUid;
    }

    public function print()
    {
        echo "STATE ";
        echo implode(' ', $this->actionSet->getActionIds());
        echo "\n";
        foreach ($this->q as $state => $qstate) {
            echo "$state ";
            echo implode(' ', $qstate);
            echo "\n";
        }
    }
}
