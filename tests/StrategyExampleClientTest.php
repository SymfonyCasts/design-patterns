<?php

namespace App\Tests;

use App\Character\Archer;
use App\Character\Warrior;
use App\Character\Mage;
use App\Game;
use PHPUnit\Framework\TestCase;

class StrategyExampleClientTest extends TestCase
{
    public function testPlay()
    {
        // pick a character
        $player = new Mage();
        $ai = new Warrior();

        $game = new Game();

        do {
            // attack phase - Player turn
            // ask for action (attack, surrender)
            $action = Game::ACTION_ATTACK;
            $game->handleAction($action, $player, $ai);

            // ai turn - it will always attack
            $game->handleAction(Game::ACTION_ATTACK, $ai, $player);
        } while(!$game->finished());

        $game->play($wizz, $warrior);
        $score = $game->getScore();
//        $game->addPlayers($wizz, $warrior);

        var_dump($score);



        // assert something on player and enemy
    }
}

