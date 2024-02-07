<?php

namespace App\Character;

use App\ArmorType\ArmorType;
use App\AttackType\AttackType;
use App\Dice;

class Character
{
    public const MAX_STAMINA = 100;

    private int $currentStamina = self::MAX_STAMINA;
    private int $currentHealth;
    private string $id;
    private string $nickname = '';
    private int $level = 1;
    private int $xp = 0;

    /**
     * XP bonus in percentage
     */
    private int $xpBonus = 0;

    public function __construct(
        private int $maxHealth,
        private int $baseDamage,
        private AttackType $attackType,
        private ArmorType $armorType
    )
    {
        $this->currentHealth = $this->maxHealth;
        $this->id = uniqid();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function attack(): int
    {
        $this->currentStamina -= (25 + Dice::roll(20));
        if ($this->currentStamina <= 0) {
            // can't attack this turn
            $this->currentStamina = self::MAX_STAMINA;

            return 0;
        }

        return $this->attackType->performAttack($this->baseDamage);
    }

    public function receiveAttack(int $damage): int
    {
        $armorReduction = $this->armorType->getArmorReduction($damage);

        $damageTaken = max($damage - $armorReduction, 0);
        $this->currentHealth -= $damageTaken;

        return $damageTaken;
    }

    public function getCurrentHealth(): int
    {
        return $this->currentHealth;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function levelUp(): void
    {
        // +%15 bonus to stats
        $bonus = 1.15;

        $this->level++;
        $this->maxHealth = floor($this->maxHealth * $bonus);
        $this->currentHealth = $this->maxHealth;
        $this->baseDamage = floor($this->baseDamage * $bonus);
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function addXp(int $xpEarned): int
    {
        $this->xp += $xpEarned * (1 + $this->xpBonus / 100);

        return $this->xp;
    }

    public function setXpBonus(int $xpBonus): void
    {
        $this->xpBonus = $xpBonus;
    }

    public function getXp(): int
    {
        return $this->xp;
    }

    public function getMaxHealth(): int
    {
        return $this->maxHealth;
    }

    public function setHealth(mixed $health): void
    {
        $this->currentHealth = $health;
    }

    public function getStamina(): int
    {
        return $this->currentStamina;
    }

    public function setStamina(int $stamina): void
    {
        $this->currentStamina = $stamina;
    }

    /**
     * Restore player's health before next fight
     */
    public function rest(): void
    {
        $this->currentHealth = $this->maxHealth;
        $this->currentStamina = self::MAX_STAMINA;
    }
}
