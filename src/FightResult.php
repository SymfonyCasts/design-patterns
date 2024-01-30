<?php

namespace App;

class FightResult
{
    private int $damageDealt = 0;
    private int $damageReceived = 0;
    private int $exhaustedTurns = 0;

    public function getDamageDealt(): int
    {
        return $this->damageDealt;
    }

    public function addDamageDealt(int $damageDealt): void
    {
        $this->damageDealt += $damageDealt;
    }

    public function getDamageReceived(): int
    {
        return $this->damageReceived;
    }

    public function addDamageReceived(int $damageReceived): void
    {
        $this->damageReceived += $damageReceived;
    }

    public function addExhaustedTurn(): void
    {
        $this->exhaustedTurns++;
    }

    public function getExhaustedTurns(): int
    {
        return $this->exhaustedTurns;
    }
}
