<?php

namespace App\Item;

use App\Character\Character;

interface ItemCommandInterface
{
    public function use(Character $character): void;

    // second part: implement "undo" functionality, it's an extra feature you can
    // easily implement when you use the command pattern
    // public function remove();
}
