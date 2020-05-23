<?php

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use RL\DQN\EGreedyAgent;
use RL\DQN\PrioritizedExperienceReplayer;
use RL\DQN\RandomBatchExperienceReplayer;
use RL\Examples\TicTacToe\Game\ConsolePlayer;
use RL\Examples\TicTacToe\Game\TicTacToe;
use RL\Examples\TicTacToe\RL\DQN\ModelProvider;
use RL\Examples\TicTacToe\RL\RandomPlayer;
use RL\Examples\TicTacToe\RL\TrainingEnvironment;

require_once(__DIR__.'/../../vendor/autoload.php');

define('GAMES_PER_EPOCH', 100);
define('EPSILON_DECAY', 0.9);
define('EPSILON_MIN', 0.001);

$trainingOpponent = new RandomPlayer();
$env = new TrainingEnvironment($trainingOpponent);

class Logger implements LoggerInterface {
    use LoggerTrait;
    public function log($level, $message, array $context = array())
    {
        if ($level != LogLevel::DEBUG) {
            echo date(DATE_ATOM) . "|[$level] $message". PHP_EOL;
        }
    }
}

$logger = new Logger();

$agent = new EGreedyAgent(
    new ModelProvider($env),
    0.95,
    1.0,
    new PrioritizedExperienceReplayer(
        32,
        1000,
        0.01,
        1.0, //reward is never > 1.0
        0.6,
        1
        //, $logger
    ),
    1000,
    $env,
    true
    //, $logger
);


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
    
    file_put_contents(__DIR__ . '/dqn.model', serialize($agent));
    $agent->setEpsilon($agent->getEpsilon() * EPSILON_DECAY);

    $logger->info("episode #$episode 
    agent played ".(($agentPlayer === TicTacToe::PLAYER_ONE) ? 'first' : 'second').
    " $w/$d/".(GAMES_PER_EPOCH-$w-$d).
    " winrate=".(($w + $d/2.0) / GAMES_PER_EPOCH * 100) . "%".
    " epsilon=".$agent->getEpsilon());
}

if ($argv['1'] === '--play') {
    $player = new ConsolePlayer();
    $env->setOpponent($player);

    // no exploration in play mode
    $agent->setEpsilon(0.0);
    $agent->setLogger($logger);

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
