<?php

namespace RL\QLearning;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RL\ActionSpace;
use RL\Agent as RLAgent;
use RL\Environment;
use RL\Verbose;

/**
 * An epsilon-greedy Q-Learning agent
 */
class EGreedyAgent implements RLAgent, Verbose
{
    private QTable $qtable;

    private float $epsilon;

    private LoggerInterface $logger;

    /**
     * @param ActionSpace $actionSpace - the ActionSpace of the system
     * @param float $epsilon - the exploration rate (probability of picking a random action)
     * @param float $learningRate - the learning rate of the q-table
     * @param float $discountFactor - the discount factor of the Bellman equation
     */
    public function __construct(
        ActionSpace $actionSpace,
        float $epsilon = 1.0,
        float $learningRate = 1.0,
        float $discountFactor = 0.995
    ) {
        $this->epsilon = $epsilon;
        $this->qtable = new QTable($actionSpace, $learningRate, $discountFactor);
        $this->logger = new NullLogger();
    }

    public function act(Environment $env): void
    {
        $state = $env->getState();

        $this->logger->debug(__CLASS__ . " state is \n" . $state->uid());
        
        if (rand(0, 1000000) / 1000000.0 < $this->epsilon) {
            $actionId = array_rand($env->getActionSpace()->getActions());
            $this->logger->info(__CLASS__ . " choosing random action #$actionId");
        } else {
            $actionId = $this->qtable->act($state);
            $this->logger->info(__CLASS__ . " choosing action #$actionId");
        }
        $reward = $env->act($actionId);

        $this->logger->debug(__CLASS__ . " got reward $reward");

        $this->qtable->learn($state, $actionId, $reward, $env->getState(), $env->isDone());
    }

    public function pickAction(\RL\State $state): int
    {
        return $this->qtable->act($state);
    }

    public function getQTable(): QTable
    {
        return $this->qtable;
    }

    public function getEpsilon(): float
    {
        return $this->epsilon;
    }

    public function setEpsilon(float $epsilon): void
    {
        $this->epsilon = $epsilon;
    }

    public function getLearningRate(): float
    {
        return $this->qtable->getLearningRate();
    }

    public function setLearningRate(float $learningRate): void
    {
        $this->qtable->setLearningRate($learningRate);
    }

    public function getDiscountFactor(): float
    {
        return $this->qtable->getDiscountFactor();
    }

    public function setDiscountFactor(float $discountFactor): void
    {
        $this->qtable->setDiscountFactor($discountFactor);
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
