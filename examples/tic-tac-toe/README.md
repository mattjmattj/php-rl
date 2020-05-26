# Tic-tac-toe

An agent using Q-Learning and self-play to learn tic-tac-toe

## State

Defining a Markov state is pretty easy here : grid + current player symbol

## Rewards

No rewards is given until the game is over.
Playing an illegal move gives a big negative reward.
A defeat is also negative, but less than an illegal move.
A draw gives a small positive reward, since the game is theoretically a draw
A win gives a big positive reward.

## Other player
The training environment is designed so that it includes the other player,
who is seen as part of the environment.

The other player is played by a random player here.

## Q-Learning

In the vanilla Q-learning part, the agent is an epsilon-greedy q-learning agent.
We start with a high epsilon, typically 1.0, that slowly decays game after game. 
That allows a good exploration in the beginning of the training, and 
then refines around the best actions.

### Training

Run

```
php train.php
```

### Playing against the agent

Simply run

```
php train.php --play
```

That will train the agent and then engage in play mode.
You will be prompted for moves ; simply type the coordinate of the cell you
want to play, as follows

```
| 0 | 1 | 2 |
| 3 | 4 | 5 |
| 7 | 8 | 9 |
```

## DQN

In the Deep Q-Learning part, the agent is an epsilon-greedy Double DQN agent.


### Model 

In this example, we will use Rubix/ML _middle_ level API to build the model

#### Input (features)

Maybe not the optimal way of implementing it. The features are as follows:
- the grid (9 x {-1.0,0.0,1.0})
- the actions (9 x {0.0,1.0})

With this design, we have to run 9 forward passes for each time the agent plays.

#### Output

A "Continuous" layer, which outputs a single float that will represent the Q value

#### Layers

2 fully-connected layers with RELU activation

### Training

Run

```
php train_dqn.php
```

### Playing against the agent

Simply run

```
php train_dqn.php --play
```

That will train the agent and then engage in play mode.
You will be prompted for moves ; simply type the coordinate of the cell you
want to play, as follows

```
| 0 | 1 | 2 |
| 3 | 4 | 5 |
| 7 | 8 | 9 |
```