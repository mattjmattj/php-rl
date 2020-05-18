<?php

namespace RL\Examples\TicTacToe\RL\DQN;

use RL\DQN\ModelProvider as DQNModelProvider;
use RL\Environment;
use Rubix\ML\Persisters\Filesystem;

class ModelProvider implements DQNModelProvider
{
    public function createModel(Environment $env): \RL\DQN\Model
    {
        return new Model($env);
    }

    public function createFromModel(\RL\DQN\Model $source): \RL\DQN\Model
    {
        if (!$source instanceof Model) {
            throw new \Exception("Cannot create Model from this source");
        }

        return new Model($source->getEnv(), unserialize(serialize($source->getNN())));
    }
}
