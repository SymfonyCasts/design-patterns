<?php

namespace App\ArmorType;

use App\Dice;

class IceBlockType implements ArmorType
{
    /**
     * Absorbs 2d4
     */
    public function getArmorReduction(int $damage): int
    {
        return Dice::roll(4) + Dice::roll(4);
    }
}
