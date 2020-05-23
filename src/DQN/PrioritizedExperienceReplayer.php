<?php

namespace RL\DQN;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Sumtree\Sumtree;

/**
 * An implementation of Prioritized Experience Replay, as described in 
 * Schaul et al. - Prioritized Experience Replay, arXiv:1511.05952, 2015
 * 
 * We store transitions in a sumtree valued by their error. The idea is to give
 * a higher priority to the transitions with the highest error
 */
class PrioritizedExperienceReplayer implements ExperienceReplayer
{
    /** maximum precision when using rand() */
    const RAND_PRECISION = 1e9;

    private int $batchSize;
    private int $replayInterval;
    private int $replayCount;

    private float $minPriority;
    private float $maxPriority;

    private float $alpha;

    private Sumtree $sumtree;

    private LoggerInterface $logger;

    /**
     * @param int $batchSize - the number of experiences for each replay batch
     * @param int $maxSize - the size of the buffer
     * @param float $minPriority - the minimum priority (epsilon), to avoid experiences with 0
     * @param float $maxPriority - upper bound of error clipping
     * @param float $alpha - [0.0,1.0] : how much prioritization is used. 0.0=uniform
     * @param int $replayInterval - the interval between each actual replay
     */
    public function __construct(
        int $batchSize,
        int $maxSize,
        float $minPriority = 0.01,
        float $maxPriority = 1.0,
        float $alpha = 0.7,
        int $replayInterval = 1,
        ?LoggerInterface $logger = null
    ) {
        $this->batchSize = $batchSize;
        $this->replayInterval = $replayInterval;
        $this->replayCount = 0;
        $this->sumtree = new Sumtree($maxSize);
        $this->minPriority = $minPriority;
        $this->maxPriority = $maxPriority;
        $this->alpha = $alpha;
        $this->logger = $logger ?? new NullLogger();
    }

    public function store(ExperienceTransition $experienceTransition): void
    {
        // we store every new experience with maximum priority, that will evolve
        // during training
        $priority = $this->sumtree->max();
        if ($priority < $this->minPriority) {
            $priority = $this->maxPriority;
        }
        $this->sumtree->add($experienceTransition, $priority);
    }

    private function sample(int $n): array
    {
        // we divide the total priority stored in the tree into batchSize segments
        $prioritySegment = $this->sumtree->sum() / $n;
             
        $minibatch = [];
        for ($i=0; $i<$n; ++$i) {
            $value = (float)rand(
                $prioritySegment * $i * self::RAND_PRECISION,
                $prioritySegment * ($i+1) * self::RAND_PRECISION
            ) / self::RAND_PRECISION;

            $minibatch[] = $this->sumtree->get($value);
        }

        return $minibatch;
    }

    public function replay(ExperienceLearner $learner): void
    {
        $this->replayCount++;
        $this->replayCount %= $this->replayInterval;
        if (0 !== $this->replayCount) {
            return;
        }
        if (count($this->sumtree) < $this->batchSize) {
            return;
        }

        $minibatch = $this->sample($this->batchSize);

        $experienceTransitions = [];
        $currentClippedPriority = 0.0;
        foreach($minibatch as list(, $value, $experienceTransition)) {
            $experienceTransitions[] = $experienceTransition;
            $currentClippedPriority += $value;
        }

        $errors = $learner->learn($experienceTransitions);

        $this->logger->info("PER. current clipped priority of minibatch=$currentClippedPriority");
        $this->logger->info("PER. error=".array_sum($errors));

        $this->update($minibatch, $errors);
    }

    /**
     * @see https://arxiv.org/abs/1511.05952 , 3.3
     */
    private function update(array $minibatch, array $errors): void
    {
        foreach($minibatch as $i => list($position, $experienceTransition)) {
            // avoiding 0 probability
            $error = max($errors[$i], $this->minPriority);

            // clipping of the upper error bound
            $error = min($this->maxPriority, $error);

            // alpha exponent
            $error **= $this->alpha;

            $this->logger->debug("PER. update #$position, value=$error");
            $this->sumtree->updateValue($position, $error);
        }
    }
}
