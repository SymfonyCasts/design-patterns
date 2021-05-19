<?php

namespace App\Character;

abstract class Character
{
    protected int $health;
    protected int $baseDamage;
    protected int $currentHealth;
    protected float $armor;


//    protected WeaponStrategy $weaponType;
//    protected ArmorStrategy $armorType;

    public function attack(Character $enemy): int
    {
        $damage = $this->baseDamage;

        return $enemy->receiveAttack($damage);
    }

    public function receiveAttack(int $enemyDamage): int
    {
        $receivedDamage = $enemyDamage * (1 - $this->armor);
        $this->currentHealth -= (int)$receivedDamage;

        return $receivedDamage;
    }

    protected function calculateDamage(): int
    {
        return $this->weaponType->getDamage($this->baseDamage);
    }

    protected function calculateDamageToReceive(int $enemyDamage): int
    {
        return (int)($enemyDamage * (1 - $this->armorReduction));
    }

    public function getCurrentHealth(): int
    {
        return $this->currentHealth;
    }
}
