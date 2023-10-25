<?php

namespace App\Builder;

use App\ArmorType\ArmorType;
use App\ArmorType\IceBlockType;
use App\ArmorType\LeatherArmorType;
use App\ArmorType\ShieldType;
use App\AttackType\AttackType;
use App\AttackType\BowType;
use App\AttackType\FireBoltType;
use App\AttackType\TwoHandedSwordType;
use App\Character\Character;

class CharacterBuilder
{
    private int $maxHealth;
    private int $baseDamage;
    private string $attackType;
    private string $armorType;

    public function setMaxHealth(int $maxHealth): self
    {
        $this->maxHealth = $maxHealth;

        return $this;
    }

    public function setBaseDamage(int $baseDamage): self
    {
        $this->baseDamage = $baseDamage;

        return $this;
    }

    public function setAttackType(string $attackType): self
    {
        $this->attackType = $attackType;

        return $this;
    }

    public function setArmorType(string $armorType): self
    {
        $this->armorType = $armorType;

        return $this;
    }

    public function buildCharacter(): Character
    {
        return new Character(
            $this->maxHealth,
            $this->baseDamage,
            $this->createAttackType(),
            $this->createArmorType(),
        );
    }

    private function createAttackType(): AttackType
    {
        return match ($this->attackType) {
            'bow' => new BowType(),
            'fire_bolt' => new FireBoltType(),
            'sword' => new TwoHandedSwordType(),
            default => throw new \RuntimeException('Invalid attack type given')
        };
    }

    private function createArmorType(): ArmorType
    {
        return match ($this->armorType) {
            'ice_block' => new IceBlockType(),
            'shield' => new ShieldType(),
            'leather_armor' => new LeatherArmorType(),
            default => throw new \RuntimeException('Invalid armor type given')
        };
    }
}
