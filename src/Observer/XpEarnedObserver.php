<?php

namespace App\Observer;

use App\FightResultSet;
use App\Service\XpCalculatorInterface;

class XpEarnedObserver implements GameObserverInterface
{
    public function __construct(
        private readonly XpCalculatorInterface $xpCalculator
    ) {
    }

    public function onFightFinished(FightResultSet $fightResultSet): void
    {
        $winner = $fightResultSet->getWinner();
        $loser = $fightResultSet->getLoser();

        $this->xpCalculator->addXp($winner, $loser->getLevel());
    }
}
