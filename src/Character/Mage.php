<?php

namespace App\Character;

class Mage extends Character
{
    public function __construct()
    {
        $this->maxHealth = 70;
        $this->currentHealth = $this->maxHealth;
        $this->baseDamage = 8;
        $this->armor = 0.10;
    }
}
