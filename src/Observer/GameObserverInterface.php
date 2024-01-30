<?php

namespace App\Observer;

use App\FightResultSet;

interface GameObserverInterface
{
    public function onFightFinished(FightResultSet $fightResultSet): void;
}
