<?php

namespace RL\DQN;

use RL\State;

interface ExperienceReplayer
{
    public function store(ExperienceTransition $experienceTransition): void;

    public function replay(ExperienceLearner $learner): void;
}
