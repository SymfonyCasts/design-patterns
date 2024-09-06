<?php

namespace App\AttackType\Ultimate;

use App\AttackType\AttackType;
use App\Dice;

class SkullBreakerSwordType implements AttackType
{
    public function performAttack(int $baseDamage): int
    {
        $swordDamage = Dice::roll(20) + Dice::roll(20) + Dice::roll(20);

        return ($baseDamage * 1.25) + $swordDamage;
    }
}
