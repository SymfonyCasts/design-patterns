<?php

namespace App\ActionCommand;

use App\Character\Character;
use App\Dice;
use App\GameApplication;

class HealCommand implements ActionCommandInterface
{
    public function __construct(private readonly Character $player)
    {
    }

    public function execute(): void
    {
        $healAmount = Dice::roll(20) + $this->player->getLevel() * 3;
        $newAmount = $this->player->getCurrentHealth() + $healAmount;
        $newAmount = min($newAmount, $this->player->getMaxHealth());

        $this->player->setHealth($newAmount);
        $this->player->setStamina(Character::MAX_STAMINA);

        GameApplication::$printer->writeln(sprintf(
            'You healed %d damage',
            $healAmount
        ));
    }
}
