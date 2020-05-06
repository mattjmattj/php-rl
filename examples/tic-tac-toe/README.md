# Tic-tac-toe

An agent using Q-Learning and self-play to learn tic-tac-toe

## State

Defining a Markov state is pretty easy here : grid + current player symbol

## Training

Run

```
php train.php
```

### Agent

The agent is an epsilon-greedy q-learning agent. We start with a high epsilon that
slowly decays game after game. That allows a good exploration in the beginning of
the training, and then refines around the best actions

### Other player
The training environment is designed so that it includes the other player,
who is seen as part of the environment.

The other player is played by a random player here.

### Rewards

No rewards is given until the game is over.
Playing an illegal move gives a big negative reward.
A defeat is also negative, but less than an illegal move.
A draw gives a small positive reward, since the game is theoretically a draw
A win gives a big positive reward.

## Playing againt the agent

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