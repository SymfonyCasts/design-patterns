<?php

namespace App\Builder;

class CharacterBuilderFactory
{
    public function createBuilder(): CharacterBuilder
    {
        return new CharacterBuilder();
    }
}
