<?php

namespace App;

use App\Character\Character;

class Game
{
    private Character $player1;
    private Character $player2;

    private array $score;
    private int $turnsPlayed = 0;

    public function play(Character $player1, Character $player2)
    {
        // reset game

        $this->player1 = $player1;
        $this->player2 = $player2;

        $this->initScore('p_1');
        $this->initScore('p_2');

        $this->doPlay();
    }

    private function doPlay()
    {
        while (!$this->didPlayerDie($this->player1) && !$this->didPlayerDie($this->player1)) {
            $this->attack($this->player1, $this->player2, 'p_1');
            $this->attack($this->player2, $this->player1, 'p_2');
            $this->turnsPlayed++;
        }
    }

    private function attack(Character $player, Character $opponent, string $index)
    {
        $damageDealt = $opponent->receiveAttack($player->attack());

        $this->score[$index]['damageDealt'] = $this->score[$index]['damageDealt'] + $damageDealt;
        $this->score[$index]['hits'] = $this->score[$index]['hits'] + 1;
    }

    private function initScore(string $index)
    {
        $this->score[$index] = [
            'damageDealt' => 0,
            'hits' => 0
        ];
    }

    public function getScore(): array
    {
        return [
            'player_1' => $this->computePlayerStats($this->player1, 'p_1'),
            'player_2' => $this->computePlayerStats($this->player2, 'p_2')
        ];
    }

    private function computePlayerStats(Character $player, $index): array
    {
        return [
            // draw if both died
            'victory' => !$this->didPlayerDie($player),
            'turns played' => $this->turnsPlayed,
            'total hits' => $this->score[$index]['hits'],
            'damage dealt' => $this->score[$index]['damageDealt'],
            // etc
        ];
    }

    private function didPlayerDie(Character $player): bool
    {
        return $player->getCurrentHealth() <= 0;
    }
}
