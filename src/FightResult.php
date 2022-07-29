<?php

namespace App;

use App\Character\Character;

class FightResult
{
    private Character $winner;
    private Character $loser;
    private int $rounds = 0;
    private int $damageDealt = 0;
    private int $damageReceived = 0;

    public function getWinner(): Character
    {
        return $this->winner;
    }

    public function setWinner(Character $winner): void
    {
        $this->winner = $winner;
    }

    public function getLoser(): Character
    {
        return $this->loser;
    }

    public function setLoser(Character $loser): void
    {
        $this->loser = $loser;
    }

    public function getRounds(): int
    {
        return $this->rounds;
    }

    public function addRound(): void
    {
        $this->rounds++;
    }

    public function getDamageDealt(): int
    {
        return $this->damageDealt;
    }

    public function addDamageDealt(int $damageDealt): void
    {
        $this->damageDealt += $damageDealt;
    }

    public function getDamageReceived(): int
    {
        return $this->damageReceived;
    }

    public function addDamageReceived(int $damageReceived): void
    {
        $this->damageReceived += $damageReceived;
    }
}
