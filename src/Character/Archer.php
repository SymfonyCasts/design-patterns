<?php

namespace App\Character;

class Archer extends Character
{
    public function __construct()
    {
        parent::__construct(80, 10, 0.15);
    }
}
