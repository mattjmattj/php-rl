<?php

namespace RL;

class ActionSet implements \Countable
{
    private array $actions = [];

    public function addAction(int $id, string $label = ''): void
    {
        $this->actions[$id] = $label;
    }

    public function removeAction(int $id): void
    {
        unset($this->actions[$id]);
    }

    public function getAction(int $id): string
    {
        return $this->actions[$id];
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function getActionIds(): array
    {
        return array_keys($this->actions);
    }

    public function count(): int
    {
        return count($this->actions);
    }

    public function findByLabel(string $label): int
    {
        return array_search($label, $this->actions);
    }
}
