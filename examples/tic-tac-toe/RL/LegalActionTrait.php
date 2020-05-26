<?php

namespace RL\Examples\TicTacToe\RL;

trait LegalActionTrait
{
    public function isActionLegal(int $actionId, State $state): bool
    {
        $g = $state->getGrid();
        return empty($g[$actionId]);
    }
}