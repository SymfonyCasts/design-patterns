<?php

namespace App\Service;

use App\Character\Character;

class OutputtingXpCalculator implements XpCalculatorInterface
{
    public function __construct(
        private readonly XpCalculatorInterface $innerCalculator
    )
    {
    }

    public function addXp(Character $winner, int $enemyLevel): void
    {
        $this->innerCalculator->addXp($winner, $enemyLevel);
    }
}
