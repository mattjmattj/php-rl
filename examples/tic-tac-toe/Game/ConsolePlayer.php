<?php

namespace RL\Examples\TicTacToe\Game;

class ConsolePlayer implements Player
{
    public function play(TicTacToe $game): void
    {
        $currentPlayer = $game->getCurrentPlayer();

        echo "You play $currentPlayer.\n";
        $game->printGrid();

        $action = readline("Enter your move: ");

        $game->play($currentPlayer, $action);
    }
}
