<?php

namespace App\ArmorType;

use App\Dice;

class ShieldType implements ArmorType
{
    /**
     * Has 15% to fully block the attack
     */
    public function getArmorReduction(int $damage): int
    {
        $chanceToBlock = Dice::roll(100);

        return $chanceToBlock > 85 ? $damage : 0;
    }
}
