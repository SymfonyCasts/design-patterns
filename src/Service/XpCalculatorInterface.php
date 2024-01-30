<?php

namespace App\Service;

use App\Character\Character;

interface XpCalculatorInterface
{
    public function addXp(Character $winner, int $enemyLevel): void;
}
