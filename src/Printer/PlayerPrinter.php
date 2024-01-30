<?php

namespace App\Printer;

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
        $this->io->writeln(sprintf('Your attack does %d damage',
            $damage
        ));
    }
}
