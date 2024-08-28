<?php

namespace App\AttackType\Ultimate;

use App\AttackType\AttackType;

class TitaniumBowType implements AttackType
{
    /**
     * Always perform a critical strike.
     */
    public function performAttack(int $baseDamage): int
    {
        return $baseDamage * 3;
    }
}
