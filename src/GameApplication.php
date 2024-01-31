<?php

namespace App;

use App\Builder\CharacterBuilder;
use App\Character\Character;
use App\Observer\GameObserverInterface;
use App\Printer\MessagePrinter;

class GameApplication
{
    public static MessagePrinter $printer;

    /** @var GameObserverInterface[] */
    private array $observers = [];

    public function play(Character $player, Character $ai): FightResultSet
    {
        $player->rest();

        $fightResultSet = new FightResultSet($player->getId(), $ai->getId());
        while (true) {
            $fightResultSet->addRound();

            // Player's turn
            $damage = $player->attack();
            if ($damage === 0) {
                self::$printer->writeln('You\'re exhausted for a turn');
                $fightResultSet->of($player)->addExhaustedTurn();
            }

            $damageDealt = $ai->receiveAttack($damage);
            $fightResultSet->of($player)->addDamageDealt($damageDealt);

            self::$printer->writeln(sprintf(
                'Your attack does %d damage',
                $damageDealt
            ));
            self::$printer->writeln('');
            usleep(300000);

            if ($this->didPlayerDie($ai)) {
                return $this->endBattle($fightResultSet, $player, $ai);
            }

            // AI's turn
            $aiDamage = $ai->attack();

            if ($damage === 0) {
                self::$printer->writeln('AI got exhausted');
                $fightResultSet->of($ai)->addExhaustedTurn();
            }

            $damageReceived = $player->receiveAttack($aiDamage);
            $fightResultSet->of($ai)->addDamageReceived($damageReceived);

            self::$printer->writeln(sprintf(
                'AI attack does %d damage',
                $aiDamage
            ));
            self::$printer->writeln('');

            if ($this->didPlayerDie($player)) {
                return $this->endBattle($fightResultSet, $ai, $player);
            }

            $this->printCurrentHealth($player, $ai);
            usleep(300000);
        }
    }

    private function endBattle(FightResultSet $fightResultSet, Character $winner, Character $loser): FightResultSet
    {
        $fightResultSet->setWinner($winner);
        $fightResultSet->setLoser($loser);

        $fightResultSet->of($winner)->addVictory();
        $fightResultSet->of($loser)->addDefeat();

        $this->notify($fightResultSet);

        return $fightResultSet;
    }

    private function didPlayerDie(Character $player): bool
    {
        return $player->getCurrentHealth() <= 0;
    }

    public function createCharacter(string $character): Character
    {
        return match (strtolower($character)) {
            'fighter' => $this->createCharacterBuilder()
                ->setMaxHealth(60)
                ->setBaseDamage(12)
                ->setAttackType('sword')
                ->setArmorType('shield')
                ->buildCharacter(),

            'archer' => $this->createCharacterBuilder()
                ->setMaxHealth(50)
                ->setBaseDamage(10)
                ->setAttackType('bow')
                ->setArmorType('leather_armor')
                ->buildCharacter(),

            'mage' => $this->createCharacterBuilder()
                ->setMaxHealth(40)
                ->setBaseDamage(8)
                ->setAttackType('fire_bolt')
                ->setArmorType('ice_block')
                ->buildCharacter(),

            'mage_archer' => $this->createCharacterBuilder()
                ->setMaxHealth(50)
                ->setBaseDamage(9)
                ->setAttackType('fire_bolt', 'bow')
                ->setArmorType('shield')
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
            'mage_archer',
        ];
    }

    private function printCurrentHealth(Character $player, Character $ai): void
    {
        self::$printer->block(sprintf(
            'Current Health: %d/%d %sAI Health: %d/%d',
            $player->getCurrentHealth(),
            $player->getMaxHealth(),
            PHP_EOL,
            $ai->getCurrentHealth(),
            $ai->getMaxHealth(),
        ));
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

    private function notify(FightResultSet $fightResultSet): void
    {
        foreach ($this->observers as $observer) {
            $observer->onFightFinished($fightResultSet);
        }
    }
}
