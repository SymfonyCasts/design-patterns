<?php

namespace App;

use App\Character\Character;

class Game
{
    const ACTION_ATTACK = 'attack';
    const ACTION_FORFEIT = 'forfeit';

    private Character $player1;
    private Character $player2;

    private array $score;
    private int $turnsPlayed = 0;
    private bool $finished = false;
    private Character $winner;

    public function handleAction($action, Character $player, Character $opponent)
    {
        switch ($action) {
            case Game::ACTION_ATTACK:
                $this->attack($player, $opponent);
                break;
            case Game::ACTION_FORFEIT:
                $this->finished = true;
                $this->winner = $opponent;
                break;
        }
    }

    private function attack(Character $player, Character $opponent)
    {
        $damageDealt = $player->attack($opponent);
        $this->score['player_1']['damageDealt'] = $this->score['player_1']['damageDealt'] + $damageDealt;
        $this->score['player_1']['hits'] = $this->score['player_1']['hits'] + 1;

//        $damageDealt = $this->player2->attack($this->player1);
//        $this->score['player_2']['damageDealt'] = $this->score['player_2']['damageDealt'] + $damageDealt;
//        $this->score['player_2']['hits'] = $this->score['player_2']['hits'] + 1;
//        $this->turnsPlayed++;
    }

    public function play(Character $player1, Character $player2)
    {
        // reset game

        $this->player1 = $player1;
        $this->player2 = $player2;

        $this->initScore('player_1');
        $this->initScore('player_2');

        $this->doPlay();
    }

    private function initScore(string $index)
    {
        $this->score[$index] = [
            'damageDealt' => 0,
            'hits' => 0
        ];
    }

    private function doPlay()
    {
        while (!$this->didPlayerDie($this->player1) && !$this->didPlayerDie($this->player1)) {
            $this->attack();
        }
    }

    public function getScore(): array
    {
        return [
            'player_1' => $this->computePlayerStats($this->player1, 'player_1'),
            'player_2' => $this->computePlayerStats($this->player2, 'player_2')
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

    public function finished(): bool
    {
        return $this->finished;
    }
}
