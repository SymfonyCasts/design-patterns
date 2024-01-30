<?php

namespace App\ArmorType;

class LeatherArmorType implements ArmorType
{
    /**
     * Absorbs 20% of the damage
     */
    public function getArmorReduction(int $damage): int
    {
        return floor($damage * 0.20);
    }
}
