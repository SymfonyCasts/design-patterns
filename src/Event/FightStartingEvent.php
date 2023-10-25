<?php

namespace App\Event;

use App\Character\Character;

class FightStartingEvent
{
    public function __construct(public Character $player, public Character $ai)
    {
    }
}
