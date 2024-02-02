<?php

namespace App\Command;

use App\AttackType\AttackType;
use App\AttackType\BowType;
use App\AttackType\FireBoltType;
use App\AttackType\TwoHandedSwordType;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:game:info')]
class GameInfoCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->section('Welcome to the game where warriors fight against each other for honor and glory... and 🍕!');

        $io->section('Character classes');
        $io->writeln('<comment>Fighter:</comment> A strong character with heavy armor and a big sword.');
        $io->writeln('<comment>Archer:</comment> An skilled character with a bow and a quiver full of arrows.');
        $io->writeln('<comment>Mage:</comment> A character with a staff and a spellbook.');
        $io->writeln('<comment>Mage Archer:</comment> The best of both worlds, it can cast spells and shoot arrows.');

        $io->section('Character Weapons');
        $swordAvgDmg = $this->computeAverageDamage('sword');
        $io->writeln(sprintf('<comment>Two Handed Sword:</comment> used by fighters. Average damage %s', $swordAvgDmg));

        $bowAvgDmg = $this->computeAverageDamage('bow');
        $io->writeln(sprintf('<comment>Bow:</comment> used by archers. Does %s times character damage', $bowAvgDmg));

        $fireBoltAvgDmg = $this->computeAverageDamage('fire_bolt');
        $io->writeln(sprintf('<comment>Fire Bolt:</comment> casted by mages. Average damage %s', $fireBoltAvgDmg));

        return Command::SUCCESS;
    }

    private function computeAverageDamage(string $attackTypeString): float
    {
        $attackType = $this->createAttackType($attackTypeString);
        $damage = 0;
        $sampleSize = 1000;
        for ($i = 0; $i < $sampleSize; $i++) {
            $damage += $attackType->performAttack(1);
        }

        return round($damage / $sampleSize, 1);
    }

    private function createAttackType(string $attackType): AttackType
    {
        return match ($attackType) {
            'bow' => new BowType(),
            'fire_bolt' => new FireBoltType(),
            'sword' => new TwoHandedSwordType(),
            default => throw new \RuntimeException('Invalid attack type given')
        };
    }
}
