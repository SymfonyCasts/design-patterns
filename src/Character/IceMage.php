<?php

namespace App\Character;

use App\Dice;

/**
 * Duplicate FireMage constructor or inherit from it (increasing inheritance level)
 */
class IceMage extends Mage
{
    public function attack(Character $target): int
    {
        return Dice::roll(4, 18);
    }
}
