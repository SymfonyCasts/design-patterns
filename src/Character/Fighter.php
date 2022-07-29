<?php

namespace App\Character;

class Fighter extends Character
{
    public function __construct()
    {
        $this->maxHealth = 90;
        $this->currentHealth = $this->maxHealth;
        $this->baseDamage = 12;
        $this->armor = 0.25;
    }
}
