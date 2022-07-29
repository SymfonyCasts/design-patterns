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
        private readonly GameApplication $game
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->text('Welcome to the game where warriors fight against each other for honor and glory!');

        $characters = $this->game->getCharactersList();
        $characterChoice = $io->choice('Select your character', $characters);

        $playerCharacter = $this->game->createCharacter($characterChoice);
        $playerCharacter->setNickname('Player ' . $characterChoice);

        $io->writeln('It\'s time for a fight!');

        $this->play($io, $playerCharacter);

        return Command::SUCCESS;
    }

    private function play(SymfonyStyle $io, Character $player): void
    {
        do {
            $aiCharacter = $this->selectAiCharacter();

            $io->writeln('Opponent found ' . $aiCharacter->getNickname());

            $fightResult = $this->game->play($player, $aiCharacter);

            $this->printResult($fightResult, $io);

            $answer = $io->choice('Want to keep playing?', [
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
        $aiCharacter->setNickname('AI: ' . ucfirst($aiCharacterString));

        return $aiCharacter;
    }

    private function printResult(FightResult $fightResult, SymfonyStyle $io)
    {
        $io->writeln('------------------------------');
        $io->writeln('Winner: ' . $fightResult->getWinner()->getNickname());
        $io->writeln('Loser: ' . $fightResult->getLoser()->getNickname());
        $io->writeln('Total Rounds: ' . $fightResult->getRounds());
        $io->writeln('Damage dealt: ' . $fightResult->getDamageDealt());
        $io->writeln('Damage received: ' . $fightResult->getDamageReceived());
        $io->writeln('------------------------------');
    }
}
