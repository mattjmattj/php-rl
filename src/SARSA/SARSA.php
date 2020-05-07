<?php

namespace RL\SARSA;

use Closure;
use RL\ActionSet;
use RL\State;

/**
 * A basic implementation of SARSA
 * SARSA is very close to Q-learning, except that it uses its own policy to update
 * Q instead of choosing the max value
 */
class SARSA
{
    private array $q;

    /** π */
    private Policy $policy;

    /** alpha */
    private float $learningRate;

    /** gamma */
    private float $discountFactor;

    private ActionSet $actionSet;

    private Closure $initializer;

    /**
     * @param ActionSet $actionSet - the ActionSet of the system
     * @param Policy $policy
     * @param float $learningRate - the learning rate (default: 1.0)
     * @param float $discountFactor - (optional) the discount factor of the Bellman equation (default: 0.995)
     * @param Closure $initialize - (optional) the callable to call when initializing a cell (default : fn() => 0)
     * @param array $q - (optional) the pre-filled table
     */
    public function __construct(
        ActionSet $actionSet,
        Policy $policy,
        float $learningRate = 1.0,
        float $discountFactor = 0.995,
        ?Closure $initializer = null,
        array $q = []
    ) {
        $this->actionSet = $actionSet;
        $this->policy = $policy;
        $this->learningRate = $learningRate;
        $this->discountFactor = $discountFactor;
        $this->initializer = $initializer ?? fn () => 0;
        $this->q = $q;
    }

    public function act(State $state): int
    {
        $stateUid = $this->initializeQForState($state);
        list($actionId,) = $this->policy->apply($this->q[$stateUid]);
        return $actionId;
    }

    public function learn(State $origin, int $actionId, float $reward, State $next, bool $done): void
    {
        $originUid = $this->initializeQForState($origin);

        if ($done) {
            // when we are done, the reward is known
            $q = $reward;
        } else {
            $nextUid = $this->initializeQForState($next);

            // Q(s,a) := Q(s,a) + α(r + γπ(Q(s')) - Q(s,a))
            $q = $this->q[$originUid][$actionId];
            $a = $this->learningRate;
            $g = $this->discountFactor;
            list(,$rnext) = $this->policy->apply($this->q[$nextUid]);
            $q = $q + $a * ($reward + $g * $rnext -$q);
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

    public function getLearningRate(): float
    {
        return $this->learningRate;
    }

    public function setLearningRate(float $learningRate): void
    {
        $this->learningRate = $learningRate;
    }

    public function getDiscountFactor(): float
    {
        return $this->discountFactor;
    }

    public function setDiscountFactor(float $discountFactor): void
    {
        $this->discountFactor = $discountFactor;
    }

    public function print(): void
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
