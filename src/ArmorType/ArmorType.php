<?php

namespace App\ArmorType;

interface ArmorType
{
    public function getArmorReduction(int $damage): int;
}
