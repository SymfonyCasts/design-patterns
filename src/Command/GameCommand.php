<?php

namespace App\Command;

use App\Character\Character;
use App\FightResult;
use App\GameApplication;
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

        // Convenient for printing messages from anywhere we need
        GameApplication::$io = $io;

        $io->section('Welcome to the game where warriors fight against each other for honor and glory... and 🍕!');

        $characters = $this->game->getCharactersList();
        $playerChoice = $io->choice('Select your character', $characters);

        $playerCharacter = $this->game->createCharacter($playerChoice);
        $playerCharacter->setNickname($this->humanize($playerChoice));

        $this->play($playerCharacter);

        return Command::SUCCESS;
    }

    private function play(Character $player): void
    {
        GameApplication::$io->writeln(sprintf('Alright %s! It\'s time to fight!',
            $player->getNickname()
        ));

        do {
            // let's make it *feel* like a proper battle!
            $weapons = ['🛡', '⚔️', '🏹'];
            GameApplication::$io->writeln(['']);
            GameApplication::$io->write('(Searching for a worthy opponent) ');
            for ($i = 0; $i < 4; $i++) {
                GameApplication::$io->write($weapons[array_rand($weapons)]);
                usleep(250000);
            }
            GameApplication::$io->writeln(['', '']);

            $aiCharacter = $this->selectAiCharacter();

            GameApplication::$io->writeln(sprintf('Opponent Found: <comment>%s</comment>', $aiCharacter->getNickname()));
            usleep(300000);

            $fightResult = $this->game->play($player, $aiCharacter);

            $this->printResult($fightResult, $player);

            $answer = GameApplication::$io->choice('Want to keep playing?', [
                1 => 'Fight!',
                2 => 'Exit Game',
            ]);
        } while ($answer === 'Fight!');
    }

    private function selectAiCharacter(): Character
    {
        $characters = $this->game->getCharactersList();
        $aiCharacterString = $characters[array_rand($characters)];

        $aiCharacter = $this->game->createCharacter($aiCharacterString, true);
        $aiCharacter->setNickname($this->humanize($aiCharacterString));

        return $aiCharacter;
    }

    private function printResult(FightResult $fightResult, Character $player): void
    {
        GameApplication::$io->writeln('');

        GameApplication::$io->writeln('------------------------------');
        if ($fightResult->getWinner() === $player) {
            GameApplication::$io->writeln('Result: <bg=green;fg=white>You WON!</>');
        } else {
            GameApplication::$io->writeln('Result: <bg=red;fg=white>You lost...</>');
        }

        GameApplication::$io->writeln('Total Rounds: ' . $fightResult->getRounds());
        GameApplication::$io->writeln('Damage dealt: ' . $fightResult->getDamageDealt());
        GameApplication::$io->writeln('Damage received: ' . $fightResult->getDamageReceived());
        GameApplication::$io->writeln('XP: ' . $player->getXp());
        GameApplication::$io->writeln('Level: ' . $player->getLevel());
        GameApplication::$io->writeln('Exhausted Turns: ' . $fightResult->getExhaustedTurns());
        GameApplication::$io->writeln('------------------------------');
    }

    private function humanize(mixed $characterChoice): string
    {
        return ucfirst(str_replace('_', ' ', $characterChoice));
    }
}
