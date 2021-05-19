<?php

namespace App\Character;

use App\Dice;

class Archer extends Character
{
    public function __construct()
    {
        $this->health = 85;
        $this->currentHealth = $this->health;
        $this->baseDamage = 10;
        $this->armor = 0.15;
    }

    public function attack(Character $target): int
    {
        return $this->baseDamage + $this->shoot();
    }

    private function shoot(): int
    {
        return Dice::roll(3, 20);
    }
}
