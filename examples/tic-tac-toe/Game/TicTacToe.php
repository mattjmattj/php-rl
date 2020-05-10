<?php

namespace RL\Examples\TicTacToe\Game;

class TicTacToe
{
    const PLAYER_ONE = 'x';
    const PLAYER_TWO = 'o';
    const DRAW = 'DRAW';

    private array $grid;

    private string $currentPlayer;

    public function __construct()
    {
        $this->grid = [
            '', '', '',
            '', '', '',
            '', '', '',
        ];
        $this->currentPlayer = self::PLAYER_ONE;
    }

    public function play(string $player, int $coordinate): void
    {
        if ($this->getWinner() === null
            && $this->currentPlayer === $player
            && $coordinate >= 0
            && $coordinate < 9
            && empty($this->grid[$coordinate])) {
            $this->grid[$coordinate] = $player;
            $this->currentPlayer = $this->currentPlayer === self::PLAYER_ONE
                ? self::PLAYER_TWO
                : self::PLAYER_ONE;
        } else {
            throw new \Exception('Invalid move or player');
        }
    }

    public function getWinner(): ?string
    {
        $g = $this->grid;

        if (!empty($g[0]) && $g[0] === $g[1] && $g[1] === $g[2]) {
            return $g[0];
        }
        if (!empty($g[3]) && $g[3] === $g[4] && $g[4] === $g[5]) {
            return $g[3];
        }
        if (!empty($g[6]) && $g[6] === $g[7] && $g[7] === $g[8]) {
            return $g[6];
        }

        if (!empty($g[0]) && $g[0] === $g[3] && $g[3] === $g[6]) {
            return $g[0];
        }
        if (!empty($g[1]) && $g[1] === $g[4] && $g[4] === $g[7]) {
            return $g[1];
        }
        if (!empty($g[2]) && $g[2] === $g[5] && $g[5] === $g[8]) {
            return $g[2];
        }

        if (!empty($g[0]) && $g[0] === $g[4] && $g[4] === $g[8]) {
            return $g[0];
        }
        if (!empty($g[2]) && $g[2] === $g[4] && $g[4] === $g[6]) {
            return $g[2];
        }

        if (array_search('', $g, true) === false) {
            return self::DRAW;
        }

        return null;
    }

    public function getCurrentPlayer(): string
    {
        return $this->currentPlayer;
    }

    public function getGrid(): array
    {
        return $this->grid;
    }

    public function printGrid(): void
    {
        $g = $this->grid;
        $g = array_map(fn ($cell) => $cell == '' ? ' ' : $cell, $g);

        echo "$g[0]|$g[1]|$g[2]\n";
        echo "-----\n";
        echo "$g[3]|$g[4]|$g[5]\n";
        echo "-----\n";
        echo "$g[6]|$g[7]|$g[8]\n";
    }
}
