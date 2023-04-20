<?php

namespace App\Observer;

use App\FightResult;

class XpEarnedObserver implements GameObserverInterface
{
    public function onFightFinished(FightResult $fightResult): void
    {
        // TODO: Implement onFightFinished() method.
    }
}
