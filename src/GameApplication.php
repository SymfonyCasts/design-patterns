<?php

namespace App;

use App\Builder\CharacterBuilder;
use App\ArmorType\IceBlockType;
use App\ArmorType\LeatherArmorType;
use App\ArmorType\ShieldType;
use App\AttackType\BowType;
use App\AttackType\FireBoltType;
use App\AttackType\MultiAttackType;
use App\AttackType\TwoHandedSwordType;
use App\Character\Character;

class GameApplication
{
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
            'fighter' => new Character(90, 12, new TwoHandedSwordType(), new ShieldType()),
            'archer' => new Character(80, 10, new BowType(), new LeatherArmorType()),
            'mage' => new Character(70, 8, new FireBoltType(), new IceBlockType()),
            'mage_archer' => new Character(75, 9, new MultiAttackType([new BowType(), new FireBoltType()]), new ShieldType()),
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
        return new CharacterBuilder();
    }
}
