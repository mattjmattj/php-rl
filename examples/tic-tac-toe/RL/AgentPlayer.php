<?php

namespace RL\Examples\TicTacToe\RL;

use RL\Agent;
use RL\Examples\TicTacToe\Game\Player;
use RL\Examples\TicTacToe\Game\TicTacToe;

class AgentPlayer implements Player
{
    private Agent $agent;

    public function __construct(Agent $agent)
    {
        $this->agent = $agent;    
    }
    public function play(TicTacToe $game): void
    {
        $actionId = $this->agent->pickAction(new State($game));

        $currentPlayer = $game->getCurrentPlayer();
        try {
            $game->play($currentPlayer, $actionId);
        }catch(\Exception $e) {
            var_dump($game);
            var_dump($actionId);
            die();
        }
    }
}
