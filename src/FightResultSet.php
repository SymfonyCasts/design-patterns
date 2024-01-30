<?php

namespace App;

use App\Character\Character;

class FightResultSet
{
    private array $results = [];
    private Character $winner;
    private Character $loser;
    private int $rounds = 0;

    public function __construct(string $playerId, string $aiId)
    {
        $this->results[$playerId] = new FightResult();
        $this->results[$aiId] = new FightResult();
    }

    public function of(Character $character): FightResult
    {
        return $this->results[$character->getId()];
    }

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
}
