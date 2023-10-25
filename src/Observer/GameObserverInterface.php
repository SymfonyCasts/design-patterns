<?php

namespace App\Observer;

use App\FightResult;

interface GameObserverInterface
{
    public function onFightFinished(FightResult $fightResult): void;
}
