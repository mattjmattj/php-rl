<?php

namespace RL\Examples\TicTacToe\RL;

use RL\Examples\TicTacToe\Game\Player;
use RL\Examples\TicTacToe\Game\TicTacToe;

class RandomPlayer implements Player
{
    public function play(TicTacToe $game): void
    {
        if (!$this->playForceWin($game)) {
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

    /**
     * We want a random player, but we never want to miss a win
     */
    public function playForceWin(TicTacToe $game): bool
    {
        $p = $game->getCurrentPlayer();
        $g = $game->getGrid();

        //rows
        for($r=0; $r<3; ++$r) {
            if ($p == $g[$r] && $p == $g[$r+1] && empty($g[$r+2])) {
                $game->play($p, $r+2);
                return true;
            }
            if ($p == $g[$r] && $p == $g[$r+2] && empty($g[$r+1])) {
                $game->play($p, $r+1);
                return true;
            }
            if ($p == $g[$r+1] && $p == $g[$r+2] && empty($g[$r])) {
                $game->play($p, $r);
                return true;
            }
        }

        //columns
        for($c=0; $c<3; ++$c) {
            if ($p == $g[$c] && $p == $g[$c+3] && empty($g[$c+6])) {
                $game->play($p, $c+6);
                return true;
            }
            if ($p == $g[$c] && $p == $g[$c+6] && empty($g[$c+3])) {
                $game->play($p, $c+3);
                return true;
            }
            if ($p == $g[$c+3] && $p == $g[$c+6] && empty($g[$c])) {
                $game->play($p, $c);
                return true;
            }
        }

        //diagonals
        if ($p == $g[0] && $p == $g[4] && empty($g[8])) {
            $game->play($p, 8);
            return true;
        }
        if ($p == $g[0] && $p == $g[8] && empty($g[4])) {
            $game->play($p, 4);
            return true;
        }
        if ($p == $g[4] && $p == $g[8] && empty($g[0])) {
            $game->play($p, 0);
            return true;
        }
        if ($p == $g[2] && $p == $g[4] && empty($g[6])) {
            $game->play($p, 6);
            return true;
        }
        if ($p == $g[0] && $p == $g[6] && empty($g[4])) {
            $game->play($p, 4);
            return true;
        }
        if ($p == $g[4] && $p == $g[6] && empty($g[2])) {
            $game->play($p, 2);
            return true;
        }

        return false;
    }
}
