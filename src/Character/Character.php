<?php

namespace App\Character;

use App\Dice;

abstract class Character
{
    protected int $baseDamage;
    protected float $armor;
    protected int $health;
    protected int $currentHealth;

    public function attack(): int
    {
        // 1d6 (1 dice of 6)
        return $this->baseDamage + Dice::roll(1, 6);
    }

    public function receiveAttack(int $damage): int
    {
        $armorReduction = (int) ($damage * $this->armor);
        $damageTaken = $damage - $armorReduction;
        $this->currentHealth -= $damageTaken;

        return $damageTaken;
    }

    public function getCurrentHealth(): int
    {
        return $this->currentHealth;
    }
}
