<?php

namespace App;

use App\Builder\CharacterBuilder;
use App\Character\Character;
use App\Observer\GameObserverInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GameApplication
{
    public static SymfonyStyle $io;

    /** @var GameObserverInterface[] */
    private array $observers = [];

    public function play(Character $player, Character $ai): FightResult
    {
        $player->rest();

        $fightResult = new FightResult();
        while (true) {
            $fightResult->addRound();

            $damage = $player->attack();
            if ($damage === 0) {
                GameApplication::$io->writeln('You\'re exhausted for a turn');
                $fightResult->addExhaustedTurn();
            }

            $damageDealt = $ai->receiveAttack($damage);
            $fightResult->addDamageDealt($damageDealt);

            GameApplication::$io->writeln(sprintf(
                'Your attack does %d damage',
                $damageDealt
            ));
            GameApplication::$io->writeln('');
            usleep(300000);

            if ($this->didPlayerDie($ai)) {
                return $this->finishFightResult($fightResult, $player, $ai);
            }

            $aiDamage = $ai->attack();

            if ($damage === 0) {
                GameApplication::$io->writeln('AI got exhausted');
                $fightResult->addExhaustedTurn();
            }

            $damageReceived = $player->receiveAttack($aiDamage);
            $fightResult->addDamageReceived($damageReceived);

            GameApplication::$io->writeln(sprintf(
                'AI attack does %d damage',
                $aiDamage
            ));
            GameApplication::$io->writeln('');

            if ($this->didPlayerDie($player)) {
                return $this->finishFightResult($fightResult, $ai, $player);
            }

            $this->printCurrentHealth($player, $ai);
            usleep(300000);
        }
    }

    private function finishFightResult(FightResult $fightResult, Character $winner, Character $loser): FightResult
    {
        $fightResult->setWinner($winner);
        $fightResult->setLoser($loser);

        $this->notify($fightResult);

        return $fightResult;
    }

    private function printCurrentHealth(Character $player, Character $ai): void
    {
        GameApplication::$io->block(sprintf(
            'Current Health: %d/%d %sAI Health: %d/%d',
            $player->getCurrentHealth(),
            $player->getMaxHealth(),
            PHP_EOL,
            $ai->getCurrentHealth(),
            $ai->getMaxHealth(),
        ));
    }

    private function didPlayerDie(Character $player): bool
    {
        return $player->getCurrentHealth() <= 0;
    }

    public function createCharacter(string $character, bool $isAi = false): Character
    {
        return match (strtolower($character)) {
            'fighter' => $this->createCharacterBuilder()
                ->setMaxHealth(60)
                ->setBaseDamage(12)
                ->setAttackType('sword')
                ->setArmorType('shield')
                ->setIsAi($isAi)
                ->buildCharacter(),

            'archer' => $this->createCharacterBuilder()
                ->setMaxHealth(50)
                ->setBaseDamage(10)
                ->setAttackType('bow')
                ->setArmorType('leather_armor')
                ->setIsAi($isAi)
                ->buildCharacter(),

            'mage' => $this->createCharacterBuilder()
                ->setMaxHealth(40)
                ->setBaseDamage(8)
                ->setAttackType('fire_bolt')
                ->setArmorType('ice_block')
                ->setIsAi($isAi)
                ->buildCharacter(),

            'mage_archer' => $this->createCharacterBuilder()
                ->setMaxHealth(50)
                ->setBaseDamage(9)
                ->setAttackType('fire_bolt', 'bow')
                ->setArmorType('shield')
                ->setIsAi($isAi)
                ->buildCharacter(),

            default => throw new \RuntimeException('Undefined Character')
        };
    }

    public function getCharactersList(): array
    {
        return [
            'fighter',
            'mage',
            'archer',
            'mage_archer'
        ];
    }

    private function createCharacterBuilder(): CharacterBuilder
    {
        return new CharacterBuilder();
    }

    public function subscribe(GameObserverInterface $observer): void
    {
        if (!in_array($observer, $this->observers, true)) {
            $this->observers[] = $observer;
        }
    }

    public function unsubscribe(GameObserverInterface $observer): void
    {
        $key = array_search($observer, $this->observers, true);

        if ($key !== false) {
            unset($this->observers[$key]);
        }
    }

    private function notify(FightResult $fightResult): void
    {
        foreach ($this->observers as $observer) {
            $observer->onFightFinished($fightResult);
        }
    }
}
