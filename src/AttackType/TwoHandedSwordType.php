<?php

namespace App\AttackType;

use App\Dice;

class TwoHandedSwordType implements AttackType
{
    public function performAttack(int $baseDamage): int
    {
        $twoHandledSwordDamage = Dice::roll(12) + Dice::roll(12);

        return $baseDamage + $twoHandledSwordDamage;
    }
}
