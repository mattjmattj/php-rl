<?php

namespace RL\Examples\TicTacToe\RL\DQN;

use RL\ActionSpace;
use RL\DQN\Model as DQNModel;
use RL\Environment;
use RL\Examples\TicTacToe\RL\State;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\NeuralNet\ActivationFunctions\ReLU;
use Rubix\ML\NeuralNet\CostFunctions\HuberLoss;
use Rubix\ML\NeuralNet\FeedForward;
use Rubix\ML\NeuralNet\Initializers\He;
use Rubix\ML\NeuralNet\Initializers\Xavier2;
use Rubix\ML\NeuralNet\Layers\Activation;
use Rubix\ML\NeuralNet\Layers\Continuous;
use Rubix\ML\NeuralNet\Layers\Dense;
use Rubix\ML\NeuralNet\Layers\Placeholder1D;
use Rubix\ML\NeuralNet\Optimizers\RMSProp;

final class Model implements DQNModel
{
    /** input = 9(grid) + 1(action) */
    const INPUT_SIZE = 10;

    private FeedForward $nn;
    private Environment $env;

    public function __construct(Environment $env, ?FeedForward $nn = null)
    {
        $this->env = $env;

        $this->nn = $nn ?? new FeedForward(
            new Placeholder1D(self::INPUT_SIZE),
            [
                new Dense(100, 0.2, true, new He()),
                new Activation(new ReLU()),
                new Dense(100, 0.2, true, new He()),
                new Activation(new ReLU()),
                new Dense(100, 0.2, true, new He()),
                new Activation(new ReLU()),
                new Dense(1, 0.0, true, new Xavier2()),
            ],
            new Continuous(
                new HuberLoss()
            ),
            new RMSProp(0.003)
        );
    }

    /**
     * Computes a prediction according to the given State
     * @return array a Q array indexed by action ids
     */
    public function predict(\RL\State $state): array
    {
        $q = [];
        foreach ($this->env->getActionSpace()->getActionIds() as $actionId) {
            // a little hack here : we do not want to consider illegal actions at all
            // for performance reasons and noise, mainly
            if (!$this->isActionLegal($actionId, $state)) {
                $q[$actionId] = -100;
            } else {
                $features = $this->stateToFeatures($state, $actionId, null);
                $output = $this->nn->infer(Unlabeled::quick([$features]))->column(0);
                $q[$actionId] = $output[0];
            }
        }

        return $q;
    }

    public function predictOne(\RL\State $state, int $actionId): float
    {
        if (!$this->isActionLegal($actionId, $state)) {
            return -100;
        }

        $features = $this->stateToFeatures($state, $actionId, null);
        $output = $this->nn->infer(Unlabeled::quick([$features]))->column(0);
        return $output[0];
    }

    private function isActionLegal(int $actionId, State $state): bool
    {
        $g = $state->getGrid();
        return empty($g[$actionId]);
    }

    /**
     * Updates the model Q prediction for the given action
     * @return float the loss
     */
    public function fit(\RL\State $state, int $actionId, float $reward): float
    {
        return $this->fitBatch([$state], [$actionId], [$reward]);
    }

    public function fitBatch(array $states, array $actionIds, array $rewards): float
    {
        $samples = [];
        $targets = [];
        foreach ($states as $k => $state) {
            $actionId = $actionIds[$k];
            $targets[] = $rewards[$k];
            $samples[] = $this->stateToFeatures($state, $actionId);
        }

        $loss = $this->nn->roundtrip(Labeled::quick($samples, $targets));
        return $loss;
    }

    private function stateToFeatures(State $state, int $actionId): array
    {
        $features = [];

        $currentPlayer = $state->getCurrentPlayer();
        
        // grid
        foreach ($state->getGrid() as $cell) {
            if (empty($cell)) {
                $features[] = 0.0;
            } else {
                $features[] = $cell === $currentPlayer ? 1.0 : -1.0;
            }
        }

        $features[] = (float)$actionId;

        return $features;
    }

    public function getNN(): FeedForward
    {
        return $this->nn;
    }

    public function setNN(FeedForward $nn): void
    {
        $this->nn = $nn;
    }

    public function getEnv(): Environment
    {
        return $this->env;
    }
}
