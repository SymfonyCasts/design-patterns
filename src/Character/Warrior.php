<?php

namespace App\Character;

class Warrior extends Character
{
    public function __construct()
    {
        $this->health = 100;
        $this->currentHealth = $this->health;
        $this->baseDamage = 12;
        $this->armor = 0.25;
    }
}
