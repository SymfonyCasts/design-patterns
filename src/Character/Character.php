<?php

namespace App\Character;

use App\Dice;

abstract class Character
{
    private int $currentHealth;
    private string $nickname = '';

    public function __construct(
        private int $maxHealth,
        private int $baseDamage,
        private float $armor
    ) {
        $this->currentHealth = $this->maxHealth;
    }

    /**
     * Damage: 1d6 (1 dice of 6)
     */
    public function attack(): int
    {
        return $this->baseDamage + Dice::roll(6);
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

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): void
    {
        $this->nickname = $nickname;
    }

    /**
     * Restore player's health before next fight
     */
    public function rest(): void
    {
        $this->currentHealth = $this->maxHealth;
    }
}
