<?php

namespace App\Printer;

use App\Character\Character;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MessagePrinter
{
    /** @var CharacterPrinterInterface[] */
    private array $printers = [];

    public function __construct(private SymfonyStyle $io, private string $playerId) {
        $this->printers[$playerId] = new PlayerPrinter($io);
        $this->printers['ai'] = new AiPrinter($io);
    }

    public function printFor(Character $character): CharacterPrinterInterface
    {
        $printerIndex = $character->getId() === $this->playerId ? $this->playerId : 'ai';

        return $this->printers[$printerIndex];
    }

    public function writeln(string|iterable $messages, int $type = OutputInterface::OUTPUT_NORMAL): void
    {
        $this->io->writeln($messages, $type);
    }

    public function write(string|iterable $messages, bool $newline = false, int $type = OutputInterface::OUTPUT_NORMAL): void
    {
        $this->io->write($messages, $newline, $type);
    }

    public function choice(string $question, array $choices, mixed $default = null, bool $multiSelect = false): mixed
    {
        return $this->io->choice($question, $choices, $default, $multiSelect);
    }

    public function block(string|array $messages, string $type = null, string $style = null, string $prefix = ' ', bool $padding = false, bool $escape = true): void
    {
        $this->io->block($messages, $type, $style, $prefix, $padding, $escape);
    }

    public function confirm(string $question, bool $default = true): bool
    {
        return $this->io->confirm($question, $default);
    }
}
