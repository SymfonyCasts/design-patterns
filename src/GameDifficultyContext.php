<?php

namespace App;

use App\Character\Character;

class GameDifficultyContext
{
    public int $level = 1;
    public int $enemyLevelBonus = 0;
    public int $enemyHealthBonus = 0;
    public int $enemyAttackBonus = 0;

    public function victory(Character $player, FightResult $fightResult): void
    {
        switch ($this->level) {
            case 1:
                if ($player->getLevel() >= 2 || $fightResult->getTotalVictories() >= 2) {
                    $this->enemyAttackBonus = 5;
                    $this->enemyHealthBonus = 5;
                    $player->setXpBonus(25);
                    $this->level++;

                    GameApplication::$printer->info('Game difficulty level increased to Medium!');
                }
                break;
            case 2:
                if ($player->getLevel() >= 4 || $fightResult->getWinStreak() >= 4) {
                    $this->enemyLevelBonus = $player->getLevel() + 1;
                    $this->enemyHealthBonus = 10;
                    $this->enemyAttackBonus = 8;
                    $player->setXpBonus(50);
                    $this->level++;

                    GameApplication::$printer->info('Game difficulty level increased to Hard!');
                }
                break;
            case 3:
                // This is like D&D style, where rolling 1 means critical failure and 20 big success
                switch (Dice::roll(20)) {
                    case 1:
                        $this->enemyLevelBonus = $player->getLevel() + 5;
                        break;
                    case 20:
                        $player->setXpBonus(100);
                        break;
                    default:
                        // restore bonus settings
                        $this->enemyLevelBonus = $player->getLevel() + 1;
                        $player->setXpBonus(50);
                        break;
                }
                break;
        }
    }

    public function defeat(Character $player, FightResult $fightResult): void
    {
        switch ($this->level) {
            case 1:
                // nothing to do
                break;
            case 2:
                // 60% chance to go back to level 1
                if (Dice::roll(100) <= 60) {
                    // Back to level 1
                    $this->enemyAttackBonus = 0;
                    $this->enemyHealthBonus = 0;
                    $player->setXpBonus(0);
                    $this->level--;

                    GameApplication::$printer->info('Game difficulty level decreased to Easy!');
                }
                break;
            case 3:
                if ($fightResult->getLoseStreak() >= 2) {
                    $this->enemyHealthBonus = 5;
                    $this->enemyAttackBonus = 5;
                    $player->setXpBonus(25);
                    $this->level--;

                    GameApplication::$printer->info('Game difficulty level decreased to Medium!');
                }
                break;
        }
    }
}
