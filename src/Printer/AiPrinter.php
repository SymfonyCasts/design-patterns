<?php

namespace App\Printer;

use App\Character\Character;
use Symfony\Component\Console\Style\SymfonyStyle;

class AiPrinter implements CharacterPrinterInterface
{
    public function __construct(private SymfonyStyle $io)
    {
    }

    public function exhaustedMessage(): void
    {
        $this->io->writeln('AI exhausted for a turn');
    }

    public function attackMessage(int $damage): void
    {
        $this->io->writeln(sprintf('AI attack does <comment>%d</comment> damage',
            $damage
        ));
    }

    public function victoryMessage(Character $player): void
    {
        $this->io->writeln('You\'ve been defeated');
    }
}
