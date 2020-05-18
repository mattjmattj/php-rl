<?php

namespace RL\DQN;

use RL\State;

interface ExperienceLearner
{
    /**
     * @param ExperienceTransition[] $experienceTransitions
     * @return float loss
     */
    public function learn(array $experienceTransitions): float;
}
