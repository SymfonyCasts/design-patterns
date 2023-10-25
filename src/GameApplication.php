<?php

namespace App;

use App\Builder\CharacterBuilder;
use App\Builder\CharacterBuilderFactory;
use App\Character\Character;
use App\Observer\GameObserverInterface;

class GameApplication
{
    /** @var GameObserverInterface[] */
    private array $observers = [];

    public function __construct(private CharacterBuilderFactory $characterBuilderFactory)
    {
    }

    public function play(Character $player, Character $ai): FightResult
    {
        $player->rest();

        $fightResult = new FightResult();
        while (true) {
            $fightResult->addRound();

            $damage = $player->attack();
            if ($damage === 0) {
                $fightResult->addExhaustedTurn();
            }

            $damageDealt = $ai->receiveAttack($damage);
            $fightResult->addDamageDealt($damageDealt);

            if ($this->didPlayerDie($ai)) {
                return $this->finishFightResult($fightResult, $player, $ai);
            }

            $damageReceived = $player->receiveAttack($ai->attack());
            $fightResult->addDamageReceived($damageReceived);

            if ($this->didPlayerDie($player)) {
                return $this->finishFightResult($fightResult, $ai, $player);
            }
        }
    }

    public function createCharacter(string $character): Character
    {
        return match (strtolower($character)) {
            'fighter' => $this->createCharacterBuilder()
                ->setMaxHealth(90)
                ->setBaseDamage(12)
                ->setAttackType('sword')
                ->setArmorType('shield')
                ->buildCharacter(),

            'archer' => $this->createCharacterBuilder()
                ->setMaxHealth(80)
                ->setBaseDamage(10)
                ->setAttackType('bow')
                ->setArmorType('leather_armor')
                ->buildCharacter(),

            'mage' => $this->createCharacterBuilder()
                ->setMaxHealth(70)
                ->setBaseDamage(8)
                ->setAttackType('fire_bolt')
                ->setArmorType('ice_block')
                ->buildCharacter(),

            'mage_archer' => $this->createCharacterBuilder()
                ->setMaxHealth(75)
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
            'mage_archer'
        ];
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

    private function finishFightResult(FightResult $fightResult, Character $winner, Character $loser): FightResult
    {
        $fightResult->setWinner($winner);
        $fightResult->setLoser($loser);

        return $fightResult;
    }

    private function didPlayerDie(Character $player): bool
    {
        return $player->getCurrentHealth() <= 0;
    }

    private function createCharacterBuilder(): CharacterBuilder
    {
        return $this->characterBuilderFactory->createBuilder();
    }
}
