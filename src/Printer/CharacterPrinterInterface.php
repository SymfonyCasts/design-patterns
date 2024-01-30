<?php

namespace App\Printer;

interface CharacterPrinterInterface
{
    public function exhaustedMessage(): void;

    public function attackMessage(int $damage): void;
}