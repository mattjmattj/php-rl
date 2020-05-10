<?php

namespace RL\Examples\TicTacToe\RL;

use RL\Examples\TicTacToe\Game\Player;
use RL\Examples\TicTacToe\Game\TicTacToe;

class RandomPlayer implements Player
{
    public function play(TicTacToe $game): void
    {
        $possibleActions = [];
        foreach ($game->getGrid() as $coord => $cell) {
            if ('' === $cell) {
                $possibleActions[] = $coord;
            }
        }
        $randomAction = $possibleActions[array_rand($possibleActions)];
        $currentPlayer = $game->getCurrentPlayer();
        $game->play($currentPlayer, $randomAction);
    }
}
