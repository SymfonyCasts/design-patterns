<?php

namespace App\Command;

use App\Character\Character;
use App\FightResult;
use App\GameApplication;
use App\Printer\MessagePrinter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:game:play')]
class GameCommand extends Command
{
    public function __construct(
        private readonly GameApplication $game,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->section('Welcome to the game where warriors fight against each other for honor and glory... and 🍕!');

        $characters = $this->game->getCharactersList();
        $playerChoice = $io->choice('Select your character', $characters);

        $playerCharacter = $this->game->createCharacter($playerChoice);
        $playerCharacter->setNickname($this->humanize($playerChoice));

        // Static field so we can print messages from anywhere
        GameApplication::$printer = new MessagePrinter($io, $playerCharacter->getId());

        $this->play($playerCharacter);

        return Command::SUCCESS;
    }

    private function play(Character $player): void
    {
        GameApplication::$printer->writeln(sprintf('Alright %s! It\'s time to fight!',
            $player->getNickname()
        ));

        do {
            // let's make it *feel* like a proper battle!
            $weapons = ['🛡', '⚔️', '🏹'];
            GameApplication::$printer->writeln(['']);
            GameApplication::$printer->write('(Searching for a worthy opponent) ');
            for ($i = 0; $i < 4; $i++) {
                GameApplication::$printer->write($weapons[array_rand($weapons)]);
                usleep(250000);
            }
            GameApplication::$printer->writeln(['', '']);

            $aiCharacter = $this->selectAiCharacter();

            GameApplication::$printer->writeln(sprintf('Opponent Found: <comment>%s</comment>', $aiCharacter->getNickname()));
            usleep(300000);

            $fightResult = $this->game->play($player, $aiCharacter);

            $this->printResult($fightResult, $player);

            $answer = GameApplication::$printer->choice('Want to keep playing?', [
                1 => 'Fight!',
                2 => 'Exit Game',
            ]);
        } while ($answer === 'Fight!');
    }

    private function selectAiCharacter(): Character
    {
        $characters = $this->game->getCharactersList();
        $aiCharacterString = $characters[array_rand($characters)];

        $aiCharacter = $this->game->createCharacter($aiCharacterString);
        $aiCharacter->setNickname($this->humanize($aiCharacterString));

        return $aiCharacter;
    }

    private function printResult(FightResult $fightResult, Character $player): void
    {
        GameApplication::$printer->writeln('');

        GameApplication::$printer->writeln('------------------------------');
        if ($fightResult->getWinner() === $player) {
            GameApplication::$printer->writeln('Result: <bg=green;fg=white>You WON!</>');
        } else {
            GameApplication::$printer->writeln('Result: <bg=red;fg=white>You lost...</>');
        }

        GameApplication::$printer->writeln('Total Rounds: ' . $fightResult->getRounds());
        GameApplication::$printer->writeln('Damage dealt: ' . $fightResult->getDamageDealt());
        GameApplication::$printer->writeln('Damage received: ' . $fightResult->getDamageReceived());
        GameApplication::$printer->writeln('XP: ' . $player->getXp());
        GameApplication::$printer->writeln('Level: ' . $player->getLevel());
        GameApplication::$printer->writeln('Exhausted Turns: ' . $fightResult->getExhaustedTurns());
        GameApplication::$printer->writeln('------------------------------');
    }

    private function humanize(mixed $characterChoice): string
    {
        return ucfirst(str_replace('_', ' ', $characterChoice));
    }
}
