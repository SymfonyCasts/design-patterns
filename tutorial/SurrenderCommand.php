<?php

namespace App\ActionCommand;

use App\Character\Character;
use App\GameApplication;

class SurrenderCommand implements ActionCommandInterface
{
    public function __construct(private readonly Character $player)
    {
    }

    public function execute(): void
    {
        $this->player->setHealth(0);

        GameApplication::$printer->block('You\'ve surrendered! Better luck next time!');
    }
}
