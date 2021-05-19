<?php

namespace App\Character;

use App\Dice;

class Mage extends Character
{
    public function __construct()
    {
        $this->health = 60;
        $this->currentHealth = $this->health;
        $this->baseDamage = 8;
        $this->armor = 0.10;
    }

    /**
     * Mage has ice block as armor which absorbs 2d6 dmg
     */
    public function receiveAttack(int $enemyDamage): int
    {
        $spellArmor = $this->iceBlock();
        $receivedDamage = $enemyDamage - $spellArmor;
        $this->currentHealth -= $receivedDamage > 0 ? $receivedDamage : 0;

        return $receivedDamage;
    }

    public function attack(Character $target): int
    {
        // Pretend this a more complex game
        return $this->castFireBolt();
    }

    /**
     * Firebolt damage: 3d6 (3 dices of six sides)
     */
    private function castFireBolt(): int
    {
        return Dice::roll(1, 6) + Dice::roll(1, 6) + Dice::roll(1, 6);
    }

    private function iceBlock(): int
    {
        return Dice::roll(1, 6) + Dice::roll(1, 6);
    }
}
