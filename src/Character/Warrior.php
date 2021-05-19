<?php

namespace App\Character;

use App\Dice;

class Warrior extends Character
{
    public function __construct()
    {
        $this->health = 100;
        $this->currentHealth = $this->health;
        $this->baseDamage = 12;
        $this->armor = 0.25;
    }

    public function attack(Character $target): int
    {
        return $this->baseDamage + $this->longSwordAttack();
    }

    /**
     * Long Sword damage: 6-12
     */
    private function longSwordAttack(): int
    {
        return Dice::roll(6, 12);
    }
}
