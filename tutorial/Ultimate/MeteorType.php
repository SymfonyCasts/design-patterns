<?php

namespace App\AttackType\Ultimate;

use App\AttackType\AttackType;
use App\Dice;

class MeteorType implements AttackType
{
    /**
     * 9d8 damage.
     */
    public function performAttack(int $baseDamage): int
    {
        return Dice::roll(8) + Dice::roll(8) + Dice::roll(8)
               + Dice::roll(8) + Dice::roll(8) + Dice::roll(8)
               + Dice::roll(8) + Dice::roll(8) + Dice::roll(8);
    }
}
