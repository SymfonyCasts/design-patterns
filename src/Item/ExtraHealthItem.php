<?php

namespace App\Item;

use App\Character\Character;

class ExtraHealthItem implements ItemCommandInterface
{
    public function use(Character $character): void
    {
        $character->maxHealth = $character->maxHealth + 15;
    }
}
