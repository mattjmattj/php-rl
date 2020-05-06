<?php

namespace RL\Examples\TicTacToe\Game;

interface Player
{
    public function play(TicTacToe $game): void;
}