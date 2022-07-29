<?php

namespace App;

use App\Character\Archer;
use App\Character\Character;
use App\Character\Fighter;
use App\Character\Mage;

class GameApplication
{
    public function play(Character $player, Character $ai): FightResult
    {
        $player->rest();

        $fightResult = new FightResult();
        while (true) {
            $fightResult->addRound();

            $damageDealt = $ai->receiveAttack($player->attack());
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
            'fighter' => new Fighter(),
            'archer' => new Archer(),
            'mage' => new Mage(),
            default => throw new \RuntimeException('Undefined Character'),
        };
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

    public function getCharactersList(): array
    {
        return [
            'fighter',
            'mage',
            'archer',
        ];
    }
}
