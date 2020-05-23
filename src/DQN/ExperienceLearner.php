<?php

namespace RL\DQN;

use RL\State;

interface ExperienceLearner
{
    /**
     * @param ExperienceTransition[] $experienceTransitions
     * @return float[] error between target and estimation, for each given transition
     */
    public function learn(array $experienceTransitions): array;
}
