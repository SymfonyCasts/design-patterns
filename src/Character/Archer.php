<?php

namespace App\Character;

use App\Dice;

class Archer extends Character
{
    public function __construct()
    {
        $this->maxHealth = 80;
        $this->currentHealth = $this->maxHealth;
        $this->baseDamage = 10;
        $this->armor = 0.15;
    }
}
