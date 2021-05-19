<?php

namespace App\Character;

use App\Dice;

class MageFighter extends Character
{
    public function __construct()
    {
        $this->health = 75;
        $this->currentHealth = $this->health;
        $this->baseDamage = 8;
        $this->armor = 0.15;
    }

    /**
     * MageFighter does not have iceBlock it uses regular armor
     */
    public function receiveAttack(int $enemyDamage): int
    {
        // duplicating code in root's base class
        // it's becoming hard to maintain this design
        $receivedDamage = $enemyDamage * (1 - $this->armor);
        $this->currentHealth -= (int)$receivedDamage;

        return $receivedDamage;
    }

    /**
     * 30% chance of casting a spell on attack
     */
    public function attack(Character $target): int
    {

    }
}
