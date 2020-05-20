<?php

namespace RL\DQN;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RL\State;
use RL\Environment;

abstract class AbstractAgent implements \RL\Agent, ExperienceLearner
{
    private ModelProvider $modelProvider;
    private Model $model;
    private Model $targetModel;
    private float $discountFactor;
    private ExperienceReplayer $replayer;
    private int $updateTargetModelInterval;
    protected LoggerInterface $logger;
    protected Environment $env;
    protected bool $useDoubleDQN;


    public function __construct(
        ModelProvider $modelProvider,
        float $discountFactor,
        ExperienceReplayer $replayer,
        int $updateTargetModelInterval,
        Environment $env,
        bool $useDoubleDQN = true
    ) {
        $this->modelProvider = $modelProvider;
        $this->discountFactor = $discountFactor;
        $this->replayer = $replayer;
        $this->updateTargetModelInterval = $updateTargetModelInterval;
        $this->logger = new NullLogger();
        $this->env = $env;
        $this->model = $this->modelProvider->createModel($env);
        $this->targetModel = $this->modelProvider->createModel($env);
        $this->useDoubleDQN = $useDoubleDQN;
    }

    /**
     * Concrete implementation must choose actions according to a policy
     * @return int action id
     */
    abstract public function chooseAction(State $state): int;

    public function act(Environment $env): void
    {
        $state = $env->getState();
        $actionId = $this->chooseAction($state);

        $reward = $env->act($actionId);

        $this->replayer->store(new ExperienceTransition(
            $state,
            $actionId,
            $reward,
            $env->getState(),
            $env->isDone()
        ));

        $this->replayer->replay($this);
    }
    
    public function updateTargetModel(): void
    {
        $this->logger->debug(__CLASS__ . ' : updating target model');
        $this->targetModel = $this->modelProvider->createFromModel($this->model);
    }

    public function learn(array $experienceTransitions): float {
        static $count;
        if (!isset($count)) {
            $count = 1;
        }

        $states = $actionIds = $rewards = [];
        foreach ($experienceTransitions as $ex) {
            $reward = $ex->reward;
            if (!$ex->done) {
                $predictionNext = $this->model->predict($ex->nextState);

                if ($this->useDoubleDQN) {
                    // double DQN : model chooses action, target estimates Q
                    $nextAction = array_search(max($predictionNext), $predictionNext);
                    $qnext = $this->targetModel->predictOne($ex->nextState, $nextAction);
                } else {
                    // vanilla DQN : target chooses action and estimates Q
                    $qnext = max($predictionNext);
                    $nextAction = array_search($qnext, $predictionNext);
                }

                $reward += $this->discountFactor * $qnext;

                $this->logger->debug("nextaction=$nextAction r=".$ex->reward."; qtarget(snext,nextaction)=$qnext");
            }
            $states[] = $ex->previousState;
            $actionIds[] = $ex->actionId;
            $rewards[] = $reward;
            $this->logger->debug("reward=$reward");
        }

        $loss = $this->model->fitBatch($states, $actionIds, $rewards);

        $this->logger->info('experience replay. loss=' . $loss);

        if ($count % $this->updateTargetModelInterval === 0) {
            $this->updateTargetModel();
        }
        ++$count;

        return $loss;
    }

    public function modelPredict(State $state): array
    {
        return $this->model->predict($state);
    }

    public function getDiscountFactor(): float
    {
        return $this->discountFactor;
    }

    public function setDiscountFactor(float $discountFactor): void
    {
        $this->discountFactor = $discountFactor;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
