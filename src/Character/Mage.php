<?php

namespace App\Character;

class Mage extends Character
{
    public function __construct()
    {
        $this->health = 60;
        $this->currentHealth = $this->health;
        $this->baseDamage = 8;
        $this->armor = 0.10;
    }
}
