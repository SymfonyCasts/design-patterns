<?php

namespace App\Printer;

use App\Character\Character;
use Symfony\Component\Console\Style\SymfonyStyle;

class PlayerPrinter implements CharacterPrinterInterface
{
    public function __construct(private SymfonyStyle $io)
    {
    }

    public function exhaustedMessage(): void
    {
        $this->io->writeln('You\'re exhausted for a turn');
    }

    public function attackMessage(int $damage): void
    {
        $this->io->writeln(sprintf('Your attack does <comment>%d</comment> damage',
            $damage
        ));
    }

    public function victoryMessage(Character $player): void
    {
        $this->io->writeln(sprintf('You\'ve slain <comment>%s</comment>', $player->getNickname()));
    }
}
