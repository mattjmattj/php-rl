<?php

namespace RL\DQN;

use RL\State;

class RandomBatchExperienceReplayer implements ExperienceReplayer
{
    private int $batchSize;
    private int $maxSize;
    private int $replayInterval;
    private int $replayCount;

    /** @var ExperienceTransition[] */
    private array $buffer;

    public function __construct(int $batchSize, int $maxSize, int $replayInterval = 1)
    {
        $this->batchSize = $batchSize;
        $this->maxSize = $maxSize;
        $this->replayInterval = $replayInterval;
        $this->replayCount = 0;
        $this->buffer = [];
    }

    public function store(ExperienceTransition $experienceTransition): void
    {
        while (count($this->buffer) >= $this->maxSize) {
            array_pop($this->buffer);
        }
        $this->buffer[] = $experienceTransition;
    }

    public function replay(ExperienceLearner $learner): void
    {
        $this->replayCount++;
        $this->replayCount %= $this->replayInterval;
        if (0 !== $this->replayCount) {
            return;
        }
        if (count($this->buffer) < $this->batchSize) {
            return;
        }

        $keysToReplay = array_rand($this->buffer, $this->batchSize);
        
        $experienceTransitions = [];
        foreach ($keysToReplay as $key) {
            $experienceTransitions[] = $this->buffer[$key];
        }
        
        $learner->learn($experienceTransitions);
    }
}
