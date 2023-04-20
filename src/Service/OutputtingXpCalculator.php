<?php

namespace App\Service;

class OutputtingXpCalculator implements XpCalculatorInterface
{
    public function __construct(
        private readonly XpCalculatorInterface $innerCalculator
    )
    {
    }
}
