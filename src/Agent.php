<?php

namespace RL;

/**
 * An RL Agent
 */
interface Agent
{
    public function act(Environment $env): void;
}
