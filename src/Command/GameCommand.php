<?php

namespace App\Command;

use App\Character\Character;
use App\Event\OutputFightStartingSubscriber;
use App\FightResult;
use App\GameApplication;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommand('app:game:play')]
class GameCommand extends Command
{
    public function __construct(
        private readonly GameApplication $game,
        private readonly EventDispatcherInterface $eventDispatcher,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->eventDispatcher->addSubscriber(new OutputFightStartingSubscriber());

        $io->text('Welcome to the game where warriors fight against each other for honor and glory... and 🍕!');

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

            $io->writeln(sprintf('Opponent found <comment>%s</comment>', $aiCharacter->getNickname()));

            $fightResult = $this->game->play($player, $aiCharacter);

            $this->printResult($fightResult, $player, $io);

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

    private function printResult(FightResult $fightResult, Character $player, SymfonyStyle $io)
    {
        // let's make it *feel* like a proper battle!
        $weapons = ['🛡', '⚔️', '🏹'];
        $io->writeln(['']);
        $io->write('(queue epic battle sounds) ');
        for ($i = 0; $i < $fightResult->getRounds(); $i++) {
            $io->write($weapons[array_rand($weapons)]);
            usleep(300000);
        }
        $io->writeln('');

        $io->writeln('------------------------------');
        if ($fightResult->getWinner() === $player) {
            $io->writeln('Result: <bg=green;fg=white>You WON!</>');
        } else {
            $io->writeln('Result: <bg=red;fg=white>You lost...</>');
        }

        $io->writeln('Total Rounds: ' . $fightResult->getRounds());
        $io->writeln('Damage dealt: ' . $fightResult->getDamageDealt());
        $io->writeln('Damage received: ' . $fightResult->getDamageReceived());
        $io->writeln('XP: ' . $player->getXp());
        $io->writeln('Final Level: ' . $player->getLevel());
        $io->writeln('Exhausted Turns: ' . $fightResult->getExhaustedTurns());
        $io->writeln('------------------------------');
    }
}
