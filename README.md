# php-rl

A reinforcement learning library in PHP

## Algorithms

### Value-based algorithms

#### SARSA

A standard state-action-reward-state-action implementation based on a Q table

#### Q-Learning

Based on a Q-table implemented as a "max" policy SARSA.
Current API provides a basic epsilon-greedy agent.
See the Tic-Tac-Toe example for some details


#### Deep Q-Learning

Current API provides a basic epsilon-greedy agent, with separated target model, as described
in Mnih, V., Kavukcuoglu, K., Silver, D. _et al_. Human-level control through deep reinforcement learning. _Nature_ **518**, 529–533 (2015). https://doi.org/10.1038/nature14236.

User can choose between a Vanilla DQN ou a Double DQN, (see Hado van Hasselt, Arthur Guez, David Silver. Deep Reinforcement Learning with Double Q-learning. [arXiv:1509.06461](https://arxiv.org/abs/1509.06461) [cs.LG])

Experience replay is available as 2 distinct implementations:
- random minibatch
- prioritized experience replay (Tom Schaul, John Quan, Ioannis Antonoglou, David Silver - Prioritized Experience Replay, [arXiv:1511.05952](https://arxiv.org/abs/1511.05952) [cs.LG], 2015)

### Policy-based algorithms

TODO

## TODO
- ~~Q-learning~~
- ~~SARSA~~
- ~~DQN~~
- ~~Double DQN~~
- ~~[DQN] prioritized experience replay~~
- Vanilla Policy Gradient (REINFORCE)
- Actor-Critic
- real documentation :)
- more examples
