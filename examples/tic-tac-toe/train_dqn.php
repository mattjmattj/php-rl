<?php

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use RL\DQN\PrioritizedExperienceReplayer;
use RL\DQN\RandomBatchExperienceReplayer;
use RL\Examples\TicTacToe\Game\ConsolePlayer;
use RL\Examples\TicTacToe\Game\Player;
use RL\Examples\TicTacToe\Game\TicTacToe;
use RL\Examples\TicTacToe\RL\AgentPlayer;
use RL\Examples\TicTacToe\RL\DQN\Agent;
use RL\Examples\TicTacToe\RL\DQN\ModelProvider;
use RL\Examples\TicTacToe\RL\RandomPlayer;
use RL\Examples\TicTacToe\RL\TrainingEnvironment;

require_once(__DIR__.'/../../vendor/autoload.php');

define('GAMES_PER_EPOCH', 100);
define('EPSILON_DECAY', 0.99);

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

$agent = new RL\Examples\TicTacToe\RL\DQN\Agent(
    new ModelProvider($env),
    0.99,
    1.0,
    new RandomBatchExperienceReplayer(32, 50000),
    5000,
    $env,
    true
    // , $logger
);


function train(Agent $agent, Player $trainingOpponent, TrainingEnvironment $env, LoggerInterface $logger, int $epochs)
{
    $episode = 0;
    while ($episode < $epochs) {
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
        
        $logger->info("episode #$episode, agent played ".(($agentPlayer === TicTacToe::PLAYER_ONE) ? 'first' : 'second').
        " $w/$d/".(GAMES_PER_EPOCH-$w-$d).
        " winrate=".(($w + $d/2.0) / GAMES_PER_EPOCH * 100) . "%".
        " epsilon=".$agent->getEpsilon());
    }
}

//100 epochs vs a random opponent
$logger->info("training against a random opponent for 300 epochs");
train($agent, $trainingOpponent, $env, $logger, 300);

//100 epochs vs itself
// $trainingOpponent = new AgentPlayer($agent);
// $env->setOpponent($trainingOpponent);

// $agent->setEpsilon(1.0);

//$logger->info("training against itself for 100 epochs");
// train($agent, $trainingOpponent, $env, $logger, 100);

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
