<?php

namespace App;

class FightResult
{
    private int $damageDealt = 0;
    private int $damageReceived = 0;
    private int $exhaustedTurns = 0;
    private int $winStreak = 0;
    private int $loseStreak = 0;
    private int $victories = 0;
    private int $defeats = 0;

    public function getDamageDealt(): int
    {
        return $this->damageDealt;
    }

    public function addDamageDealt(int $damageDealt): void
    {
        $this->damageDealt += $damageDealt;
    }

    public function removeDamageDealt(int $damageDealt): void
    {
        $this->damageDealt -= $damageDealt;
    }

    public function getDamageReceived(): int
    {
        return $this->damageReceived;
    }

    public function addDamageReceived(int $damageReceived): void
    {
        $this->damageReceived += $damageReceived;
    }

    public function removeDamageReceived(int $damageReceived): void
    {
        $this->damageReceived -= $damageReceived;
    }

    public function addExhaustedTurn(): void
    {
        $this->exhaustedTurns++;
    }

    public function getExhaustedTurns(): int
    {
        return $this->exhaustedTurns;
    }

    public function getWinStreak(): int
    {
        return $this->winStreak;
    }

    public function getLoseStreak(): int
    {
        return $this->loseStreak;
    }

    public function getTotalVictories(): int
    {
        return $this->victories;
    }

    public function addVictory(): void
    {
        $this->victories++;
        $this->winStreak++;
        $this->loseStreak = 0;
    }

    public function addDefeat(): void
    {
        $this->defeats++;
        $this->loseStreak++;
        $this->winStreak = 0;
    }

    public function prepareForNextMatch(): void
    {
        $this->damageDealt = 0;
        $this->damageReceived = 0;
        $this->exhaustedTurns = 0;
    }
}
