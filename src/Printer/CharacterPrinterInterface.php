<?php

namespace App\Printer;

use App\Character\Character;

interface CharacterPrinterInterface
{
    public function exhaustedMessage(): void;

    public function attackMessage(int $damage): void;

    public function victoryMessage(Character $player);
}
