<?php

namespace RL\Examples\TicTacToe\RL;

use RL\ActionSet;
use RL\Examples\TicTacToe\Game\TicTacToe;

use RL\Environment;
use RL\Examples\TicTacToe\Game\Player;

class TrainingEnvironment implements Environment
{
    private TicTacToe $game;

    private ActionSet $actionSet;

    private Player $opponent;

    public function __construct(Player $opponent)
    {
        $this->opponent = $opponent;
        $this->reset();
    }

    public function reset(): void
    {
        $this->game = new TicTacToe();

        $this->actionSet = new ActionSet();
        $this->actionSet->addAction(0, 'A3');
        $this->actionSet->addAction(1, 'B3');
        $this->actionSet->addAction(2, 'C3');
        $this->actionSet->addAction(3, 'A2');
        $this->actionSet->addAction(4, 'B2');
        $this->actionSet->addAction(5, 'C2');
        $this->actionSet->addAction(6, 'A1');
        $this->actionSet->addAction(7, 'B1');
        $this->actionSet->addAction(8, 'C1');
    }

    public function getActionSet(): ActionSet
    {
        return $this->actionSet;
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
            return -10;
        }

        if ($this->game->getWinner() === $currentPlayer) {
            // agent won
            return 10;
        }

        if ($this->game->getWinner() === TicTacToe::DRAW) {
            // the game is a draw
            return 5;
        }

        // now the random opponent plays
        $currentPlayer = $this->game->getCurrentPlayer();
        $this->opponent->play($this->game);

        if ($this->game->getWinner() === $currentPlayer) {
            // random opponent won
            return -5;
        }

        // draw or still playing
        return 0;
    }

    public function isDone(): bool
    {
        return $this->game->getWinner() !== null;
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
