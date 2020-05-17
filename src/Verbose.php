<?php

namespace RL;

use Psr\Log\LoggerInterface;

interface Verbose
{
    public function setLogger(LoggerInterface $logger): void;
}
