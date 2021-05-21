<?php

namespace App;

class Dice
{
    /**
     * It simulates the roll of a dice of N sides
     */
    public static function roll(int $sides): int
    {
        return random_int(1, $sides);
    }
}
