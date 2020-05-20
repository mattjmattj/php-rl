<?php

namespace RL\Test\SARSA;

use PHPUnit\Framework\TestCase;
use RL\ActionSpace;
use RL\SARSA\Policy;
use RL\SARSA\SARSA;
use RL\State;

final class SARSATest extends TestCase
{
    public function setUp(): void
    {
        $this->A = new SARSATestState('A');
        $this->B = new SARSATestState('A');
        $this->C = new SARSATestState('A');
        $this->policy = new SARSATestPolicy();
    }

    public function testCanSetAndGetTable(): void
    {
        $table = [[0,0,0],[1,1,1],[0,0,0]];
        $actionSpace = new ActionSpace();
        $actionSpace->addAction(0);
        $actionSpace->addAction(1);
        $actionSpace->addAction(2);

        $sarsa = new SARSA($actionSpace, $this->policy, 1.0, 0.95, null, $table);

        $this->assertEquals($table, $sarsa->getTable());
    }

    public function testChoosesActionBasedOnAGivenPolicy(): void
    {
        $table = [
            'A' => [0,1,0],
            'B' => [1,1,5],
            'C' => [0,-1,0.3]
        ];
        $actionSpace = new ActionSpace();
        $actionSpace->addAction(0);
        $actionSpace->addAction(1);
        $actionSpace->addAction(2);

        $sarsa = new SARSA($actionSpace, $this->policy, 1.0, 0.95, null, $table);

        $this->assertEquals(1, $sarsa->act(new SARSATestState('A')));
        $this->assertEquals(2, $sarsa->act(new SARSATestState('B')));
        $this->assertEquals(2, $sarsa->act(new SARSATestState('C')));
    }

    public function testCanLearnUsingBellmanEquationAndAGivenPolicy(): void
    {
        $table = [
            'A' => [1,0,0],
            'B' => [0,2,0],
            'C' => [0,0,0]
        ];
        $actionSpace = new ActionSpace();
        $actionSpace->addAction(0);
        $actionSpace->addAction(1);
        $actionSpace->addAction(2);

        $sarsa = new SARSA($actionSpace, $this->policy, 1.0, 0.9, null, $table);

        $sarsa->learn(
            new SARSATestState('A'),
            0,
            0.5,
            new SARSATestState('B'),
            false
        );

        $this->assertEquals(
            [
                'A' => [2.3,0,0],
                'B' => [0,2,0],
                'C' => [0,0,0]
            ],
            $sarsa->getTable()
        );

        $sarsa->learn(
            new SARSATestState('B'),
            1,
            3,
            new SARSATestState('B'),
            true
        );

        $this->assertEquals(
            [
                'A' => [2.3,0,0],
                'B' => [0,3,0],
                'C' => [0,0,0]
            ],
            $sarsa->getTable()
        );
    }

    public function testCanHaveALearningRate(): void
    {
        $table = [
            'A' => [1,0,0],
            'B' => [0,2,0],
            'C' => [0,0,0]
        ];
        $actionSpace = new ActionSpace();
        $actionSpace->addAction(0);
        $actionSpace->addAction(1);
        $actionSpace->addAction(2);

        $sarsa = new SARSA($actionSpace, $this->policy, 0.5, 0.9, null, $table);

        $sarsa->learn(
            new SARSATestState('A'),
            0,
            0.5,
            new SARSATestState('B'),
            false
        );

        $this->assertEquals(
            [
                'A' => [1.65,0,0],
                'B' => [0,2,0],
                'C' => [0,0,0]
            ],
            $sarsa->getTable()
        );
    }
}

final class SARSATestState implements State
{
    public string $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function uid(): string
    {
        return $this->code;
    }
}

final class SARSATestPolicy implements Policy
{
    public function apply(array $qstate): array
    {
        $reward = max($qstate);
        $actionId = array_search($reward, $qstate);
        return [$actionId, $reward];
    }
}
