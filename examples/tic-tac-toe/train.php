<?php

use RL\Examples\TicTacToe\Game\ConsolePlayer;
use RL\Examples\TicTacToe\Game\TicTacToe;
use RL\Examples\TicTacToe\RL\RandomPlayer;
use RL\Examples\TicTacToe\RL\TrainingEnvironment;
use RL\QLearning\EGreedyAgent;

require_once(__DIR__.'/../../vendor/autoload.php');

define('GAMES_PER_EPOCH', 100);
define('EPSILON_DECAY', 0.97);
define('EPSILON_MIN', 0.0001);

$trainingOpponent = new RandomPlayer();
$env = new TrainingEnvironment($trainingOpponent);

$agent = new EGreedyAgent($env->getActionSet(), 1.0, 1.0);

$episode = 0;
while ($agent->getEpsilon() > EPSILON_MIN) {
    $episode++;
    $games = GAMES_PER_EPOCH;
    $w = $d = 0;
    while ($games--) {
        if ($episode % 2) {
            $agentPlayer = TicTacToe::PLAYER_TWO;
            $trainingOpponent->play($env->getGame());
        } else {
            $agentPlayer = TicTacToe::PLAYER_ONE;
        }

        while (!$env->isDone()) {
            $agent->act($env);
        }

        $winner = $env->getGame()->getWinner();
        if ($winner === $agentPlayer) {
            $w++;
        }
        if ($winner === TicTacToe::DRAW) {
            $d++;
        }

        $env->reset();
    }

    $agent->setEpsilon($agent->getEpsilon() * EPSILON_DECAY);

    echo "episode #$episode\n";
    echo "agent played ".(($agentPlayer === TicTacToe::PLAYER_ONE) ? 'first' : 'second')."\n";
    echo "$w wins, $d draws, ".(GAMES_PER_EPOCH-$w-$d)." loss\n";
    echo '' . (($w + $d/2.0) / GAMES_PER_EPOCH * 100) . "%\n";
    echo "epsilon : ".$agent->getEpsilon()."\n\n";
}

if ($argv['1'] === '--play') {
    $player = new ConsolePlayer();
    $env->setOpponent($player);

    // no exploration in play mode
    $agent->setEpsilon(0.0);

    $games = 0;
    while (true) {
        echo "New game !\n";
        $env->reset();

        if ($games % 2) {
            $player->play($env->getGame());
        }
        while (!$env->isDone()) {
            $agent->act($env);
        }
        echo $env->getGame()->printGrid();
        echo "\n";
        $winner = $env->getGame()->getWinner();
        echo "Winner : $winner\n\n";

        $games++;
    }
}
