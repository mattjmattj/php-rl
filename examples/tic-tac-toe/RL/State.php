<?php

namespace RL\Examples\TicTacToe\RL;

use RL\State as RLState;
use RL\Examples\TicTacToe\Game\TicTacToe;

class State implements RLState
{
    private string $uid;

    private array $grid;

    private string $currentPlayer;

    public function __construct(TicTacToe $game)
    {
        $this->grid = $game->getGrid();
        $this->currentPlayer = $game->getCurrentPlayer();
        $this->defineUid($game);
    }

    private function defineUid(TicTacToe $game)
    {
        $g = $game->getGrid();
        $g = array_map(fn ($cell) => $cell == '' ? ' ' : $cell, $g);
        $player = $game->getCurrentPlayer();
        $this->uid = <<<EOT
player: $player
$g[0]|$g[1]|$g[2]
-----
$g[3]|$g[4]|$g[5]
-----
$g[6]|$g[7]|$g[8]
EOT;
    }
   
    public function uid(): string
    {
        return $this->uid;
    }

    public function getGrid(): array
    {
        return $this->grid;
    }

    public function getCurrentPlayer(): string
    {
        return $this->currentPlayer;
    }
}
