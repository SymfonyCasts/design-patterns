<?php

namespace App\AttackType;

class MultiAttackType implements AttackType
{
    /**
     * @param AttackType[] $attackTypes
     */
    public function __construct(private array $attackTypes)
    {
    }

    public function performAttack(int $baseDamage): int
    {
        $type = $this->attackTypes[array_rand($this->attackTypes)];

        return $type->performAttack($baseDamage);
    }
}
