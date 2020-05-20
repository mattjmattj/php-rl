<?php

namespace RL\Examples\TicTacToe\RL;

use RL\ActionSpace;
use RL\Examples\TicTacToe\Game\TicTacToe;

use RL\Environment;
use RL\Examples\TicTacToe\Game\Player;

class TrainingEnvironment implements Environment
{
    private TicTacToe $game;

    private ActionSpace $actionSpace;

    private Player $opponent;

    private bool $gameOver;

    public function __construct(Player $opponent)
    {
        $this->opponent = $opponent;
        $this->reset();
    }

    public function reset(): void
    {
        $this->game = new TicTacToe();

        $this->actionSpace = new ActionSpace();
        $this->actionSpace->addAction(0, 'A3');
        $this->actionSpace->addAction(1, 'B3');
        $this->actionSpace->addAction(2, 'C3');
        $this->actionSpace->addAction(3, 'A2');
        $this->actionSpace->addAction(4, 'B2');
        $this->actionSpace->addAction(5, 'C2');
        $this->actionSpace->addAction(6, 'A1');
        $this->actionSpace->addAction(7, 'B1');
        $this->actionSpace->addAction(8, 'C1');

        $this->gameOver = false;
    }

    public function getActionSpace(): ActionSpace
    {
        return $this->actionSpace;
    }

    public function getState(): State
    {
        return new State($this->game);
    }

    public function act(int $actionId): float
    {
        $currentPlayer = $this->game->getCurrentPlayer();
        try {
            $this->game->play($currentPlayer, $actionId);
        } catch (\Exception $e) {
            // an exception means an invalid action : negative reward
            $this->gameOver = true;
            return -1.0;
        }

        if ($this->game->getWinner() === $currentPlayer) {
            // agent won
            $this->gameOver = true;
            return 1.0;
        }

        if ($this->game->getWinner() === TicTacToe::DRAW) {
            // the game is a draw
            $this->gameOver = true;
            return 0.2;
        }

        // now the opponent plays
        $currentPlayer = $this->game->getCurrentPlayer();
        $this->opponent->play($this->game);

        if ($this->game->getWinner() === $currentPlayer) {
            // opponent won
            $this->gameOver = true;
            return -0.2;
        }

        if ($this->game->getWinner() === TicTacToe::DRAW) {
            // the game is a draw
            $this->gameOver = true;
            return 0.2;
        }

        // draw or still playing
        return 0.0;
    }

    public function isDone(): bool
    {
        return $this->gameOver;
    }

    public function getGame(): TicTacToe
    {
        return $this->game;
    }

    public function setOpponent(Player $player): void
    {
        $this->opponent = $player;
    }
}
