<?php

namespace App;

class Dice
{
    public static function roll(int $min, int $max): int
    {
        return random_int($min, $max);
    }
}
