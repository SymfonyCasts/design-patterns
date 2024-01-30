<?php

namespace App\Printer;

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
        $this->io->writeln(sprintf('AI attack does %d damage',
            $damage
        ));
    }
}
