<?php

namespace RL\Test\QLearning;

use phpDocumentor\Reflection\Types\Void_;
use PHPUnit\Framework\TestCase;
use RL\ActionSet;
use RL\QLearning\QTable;
use RL\State;

final class QTableTest extends TestCase
{
    public function setUp(): void
    {
        $this->A = new QTableTestState('A');
        $this->B = new QTableTestState('A');
        $this->C = new QTableTestState('A');
    }

    public function testCanSetAndGetTable(): void
    {
        $table = [[0,0,0],[1,1,1],[0,0,0]];
        $actionSet = new ActionSet();
        $actionSet->addAction(0);
        $actionSet->addAction(1);
        $actionSet->addAction(2);

        $qtable = new QTable($actionSet, 1.0, 0.95, null, $table);

        $this->assertEquals($table, $qtable->getTable());
    }

    public function testChoosesActionBasedOnMaxExpectedRewarded(): void
    {
        $table = [
            'A' => [0,1,0],
            'B' => [1,1,5],
            'C' => [0,-1,0.3]
        ];
        $actionSet = new ActionSet();
        $actionSet->addAction(0);
        $actionSet->addAction(1);
        $actionSet->addAction(2);

        $qtable = new QTable($actionSet, 1.0, 0.95, null, $table);

        $this->assertEquals(1, $qtable->act(new QTableTestState('A')));
        $this->assertEquals(2, $qtable->act(new QTableTestState('B')));
        $this->assertEquals(2, $qtable->act(new QTableTestState('C')));
    }

    public function testCanLearnUsingBellmanEquation(): void
    {
        $table = [
            'A' => [1,0,0],
            'B' => [0,2,0],
            'C' => [0,0,0]
        ];
        $actionSet = new ActionSet();
        $actionSet->addAction(0);
        $actionSet->addAction(1);
        $actionSet->addAction(2);

        $qtable = new QTable($actionSet, 1.0, 0.9, null, $table);

        $qtable->learn(
            new QTableTestState('A'),
            0,
            0.5,
            new QTableTestState('B'),
            false
        );

        $this->assertEquals(
            [
                'A' => [2.3,0,0],
                'B' => [0,2,0],
                'C' => [0,0,0]
            ],
            $qtable->getTable()
        );

        $qtable->learn(
            new QTableTestState('B'),
            1,
            3,
            new QTableTestState('B'),
            true
        );

        $this->assertEquals(
            [
                'A' => [2.3,0,0],
                'B' => [0,3,0],
                'C' => [0,0,0]
            ],
            $qtable->getTable()
        );
    }

    public function testCanHaveALearningRate(): void
    {
        $table = [
            'A' => [1,0,0],
            'B' => [0,2,0],
            'C' => [0,0,0]
        ];
        $actionSet = new ActionSet();
        $actionSet->addAction(0);
        $actionSet->addAction(1);
        $actionSet->addAction(2);

        $qtable = new QTable($actionSet, 0.5, 0.9, null, $table);

        $qtable->learn(
            new QTableTestState('A'),
            0,
            0.5,
            new QTableTestState('B'),
            false
        );

        $this->assertEquals(
            [
                'A' => [1.65,0,0],
                'B' => [0,2,0],
                'C' => [0,0,0]
            ],
            $qtable->getTable()
        );
    }
}

final class QTableTestState implements State
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
