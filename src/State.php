<?php

namespace RL;

/**
 * A representation of a Markov state for the current system
 */
interface State
{
    public function uid(): string;
}
