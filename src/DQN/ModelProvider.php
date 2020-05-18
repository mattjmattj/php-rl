<?php

namespace RL\DQN;

use RL\Environment;

interface ModelProvider
{
    public function createModel(Environment $env): Model;

    public function createFromModel(Model $source): Model;
}
