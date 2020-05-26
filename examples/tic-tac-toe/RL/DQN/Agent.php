<?php

namespace RL\Examples\TicTacToe\RL\DQN;

use RL\DQN\EGreedyAgent;
use RL\Examples\TicTacToe\RL\LegalActionTrait;
use RL\Examples\TicTacToe\RL\State as RLState;
use RL\State;

class Agent extends EGreedyAgent
{
    use LegalActionTrait;

    /**
     * override to pick only legal moves, for faster training
     */
    protected function chooseRandomAction(State $state): int
    {
        $availableActions = [];
        foreach ($this->env->getActionSpace()->getActionIds() as $actionId) {
            if ($this->isActionLegal($actionId, $state)) {
                $availableActions[] = $actionId;
            }
        }
        return $availableActions[array_rand($availableActions)];
    }
}